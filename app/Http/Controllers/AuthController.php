<?php
namespace App\Http\Controllers;

use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class AuthController extends Controller
{
    // ------------------ SHOW REGISTER FORM --------------------
    public function showRegisterForm(Request $request)
    {
        $referralCode = $request->query('referral');  // ABCD1234
        return view('auth.register', compact('referralCode'));
    }

    // ------------------ FORM REGISTRATION --------------------
    public function register(Request $request)
    {
        // Validation (one error at a time)
        $validator = \Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return back()->withErrors($firstError)->withInput();
        }

        // Referral Logic
        $refUser = User::where('referral_code', $request->referral_code)->first();
        $referredBy = $refUser->id ?? null;

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'referral_code' => Str::upper(Str::random(8)),
            'referred_by'   => $referredBy,
        ]);

        auth()->login($user);

        return redirect('/dashboard');
    }

    // ------------------ GOOGLE LOGIN --------------------
    public function redirectToGoogle(Request $request)
    {
        // Save referral code in session (if exists)
        session(['referral_code' => $request->query('referral')]);

        return Socialite::driver('google')->redirect();
    }

    // ------------------ GOOGLE CALLBACK --------------------
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $socialId = $googleUser->getId();
        $provider = 'google';
        $email    = $googleUser->getEmail() ?? null;
        
        $fullname = $googleUser->name ?? 'User';

        // Check if user already exists by social_id + provider
        // $user = User::where('authenticationSocialId', $socialId)
        //             ->where('authenticationProvider', $provider)
        //             ->first();

              
        // Check existing user
        $existingUser = User::where('email', $googleUser->getEmail())
                            ->where('authenticationProvider', 'google')
                            ->first();

        if ($existingUser) {
            auth()->login($existingUser);
            return redirect('/dashboard');
        }

        // Referral from session
        $referralCode = session('referral_code');
        $refUser = User::where('referral_code', $referralCode)->first();
        $referredBy = $refUser->id ?? null;

        // Create new user
        $user = User::create([
            'name'          => $googleUser->getName(),
            'email'         => $googleUser->getEmail(),
            'authenticationSocialId'     => $googleUser->getId(),
            'authenticationProvider' => 'google',   
            'referral_code' => Str::upper(Str::random(8)),
            'referred_by'   => $referredBy,
            'profile'=> $googleUser->getAvatar(),
            'used_referral_code'     => $referralCode,
            'referred_by'            => $referredBy,
            'email_verified_at'      => Carbon::now(),
        ]);

        auth()->login($user);

        return redirect('/dashboard');
    }
}
