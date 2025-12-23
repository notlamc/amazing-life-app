<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use App\Models\Admin\UserSubscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel API Docs",
 *      description="Sanctum Auth API Documentation"
 * )
 */
class AuthController extends Controller
{
    /**
         * @OA\Post(
         *      path="/api/register",
         *      summary="Register user",
         *      tags={"Authentication"},
         *      @OA\RequestBody(
         *          required=true,
         *          @OA\JsonContent(
         *              required={"fullname","email","password","password_confirmation"},
         *              @OA\Property(property="fullname", type="string"),
         *              @OA\Property(property="email", type="string"),
         *              @OA\Property(property="password", type="string"),
         *              @OA\Property(property="password_confirmation", type="string"),
         *              @OA\Property(property="referral_code", type="string"),
         *          ),
         *      ),
         *      @OA\Response(
         *          response=200,
         *          description="Success",
         *      )
         * )
     */    

    public function register(Request $request)
    {
        try {
            // Validate incoming data
            $validated = $request->validate([
                'fullname'                 => 'required|string|max:255',
                'email'                    => 'required|email',
                'password'                 => 'required|string|min:8|confirmed',
                'password_confirmation'    => 'required',
                'referral_code'    => 'nullable|string|max:255',

            ]);

            // Trim input
            $email = trim($request->email);
            $password = trim($request->password);

            // Check if password is blank
            if ($password === '') {
                return response()->json([
                    "status"  => false,
                    "message" => 'Password cannot be blank.'
                ], 422);
            }

            // Check if email exists
            if (User::where('email', $email)->exists()) {
                return response()->json([
                    "status"  => false,
                    "message" => 'Email already exists.'
                ], 422);
            }

            // Create user
            $user = User::create([
                "name"          => $validated['fullname'],
                "email"         => $validated['email'],
                "password"      => Hash::make($validated['password']),
                "referral_code" => Str::upper(Str::random(8)),
                "status"        => 'active', // default user status
            ]);

            return response()->json([
                "status"  => true,
                "message" => "User registered successfully",
                "user"    => $user
            ], 201);

        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->first();
            return response()->json([
                "status"  => false,
                "message" => is_array($firstError) ? $firstError[0] : $firstError
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status"  => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }


    /**
         * @OA\Post(
         *      path="/api/socialLoginRegister",
         *      summary="Social Login Registration",
         *      tags={"Authentication"},
         *      @OA\RequestBody(
         *          required=true,
         *          @OA\JsonContent(
         *              required={"authenticationSocialId","authenticationProvider"},
         *              @OA\Property(property="fullname", type="string"),
         *              @OA\Property(property="email", type="string"),
         *              @OA\Property(property="authenticationSocialId", type="string"),
         *              @OA\Property(property="authenticationProvider", type="string"),
         *              @OA\Property(property="referral_code", type="string"),
         *          ),
         *      ),
         *      @OA\Response(
         *          response=200,
         *          description="Success",
         *      )
         * )
    */  
    public function socialLoginRegister(Request $request)
    {
        try {
            // Validate required fields
            $validated = $request->validate([
                'authenticationSocialId'   => 'required|string',
                'email'       => 'nullable|email',
                'fullname'    => 'nullable|string|max:255',
                'referral_code'    => 'nullable|string|max:255',
                'authenticationProvider'    => 'required|string|in:facebook,google,apple',
            ], [
                'authenticationSocialId.required' => 'Authentication Social ID is required.',
                'authenticationProvider.in'        => 'Authentication Provider must be facebook, google, or apple.',
            ]);

            $socialId = $validated['authenticationSocialId'];
            $provider = $validated['authenticationProvider'];
            $email    = $validated['email'] ?? null;
            $referral_code    = $validated['referral_code'] ?? null;
            $fullname = $validated['fullname'] ?? 'User';

            // Check if user already exists by social_id + provider
            $user = User::where('authenticationSocialId', $socialId)
                        ->where('authenticationProvider', $provider)
                        ->first();

            $user_referral_code = User::where('referral_code', $referral_code)->first();
            $referredBy = $user_referral_code ? $user_referral_code->id : null;

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name'                   => $fullname,
                    'email'                  => $email,
                    'authenticationSocialId' => $socialId,
                    'authenticationProvider' => $provider,                    
                    'referral_code'          => Str::upper(Str::random(8)),
                    'used_referral_code'     => $referral_code,
                    'referred_by'            => $referredBy,
                    'email_verified_at'      => Carbon::now(),
                ]);
            }
            // Generate token
            $token = $user->createToken("api_token")->plainTextToken;

             // Check subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                            ->where('is_active', 1)
                            ->whereDate('end_date', '>=', Carbon::today())
                            ->first();
            $subscriptionActive = $subscription ? true : false;

            return response()->json([
                "status"  => true,
                "message" => ucfirst($provider)." logged in successfully",
                "token"   => $token,
                "user"    => $user,
                "subscriptionActive"  => $subscriptionActive
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->first();
            return response()->json([
                "status"  => false,
                "message" => is_array($firstError) ? $firstError[0] : $firstError
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status"  => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      summary="Login user",
     *      tags={"Authentication"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login success",
     *      )
     * )
     */
    public function login(Request $request)
    {
        try {
            // Custom validator (returns one error only)
            // Validate incoming data
            $validated = $request->validate([
                'email'                    => 'required|email',
                'password'                 => 'required|string|min:8',
                

            ]);
            
            // Check user exists
            $user = User::where("email", $request->email)->first();

            if (!$user) {
                return response()->json([
                    "status"  => false,
                    "message" => "User not found"
                ], 404);
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    "status"  => false,
                    "message" => "Invalid Credentials"
                ], 401);
            }

            // Generate token
            $token = $user->createToken("api_token")->plainTextToken;

             // Check subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                            ->where('is_active', 1)
                            ->whereDate('end_date', '>=', Carbon::today())
                            ->first();

            $subscriptionActive = $subscription ? true : false;

            return response()->json([
                "status"  => true,
                "message" => "Login successful",
                "token"   => $token,
                "user"    => $user,
                "subscriptionActive"  => $subscriptionActive
            ], 200);

        } catch (\Throwable $e) {

            \Log::error("Login Error: " . $e->getMessage());

            return response()->json([
                "status"  => false,
                "message" => "Something went wrong",
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      summary="Logout user",
     *      tags={"Authentication"},
     *      security={{"Bearer":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Logged out"
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(["message" => "Logged out"]);
    }
}
