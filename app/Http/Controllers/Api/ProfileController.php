<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\UserSubscription;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/userProfileByToken",
     *     summary="Get authenticated user profile",
     *     description="This API returns the profile of the logged-in user using Sanctum Bearer Token.",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profile fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile data fetched successfully"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function userProfileByTokens(Request $request)
    {
        return response()->json([
            "status"  => true,
            "message" => "Profile data fetched successfully",
            "user"    => $request->user(), // Sanctum authenticated user
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/check-subscription",
     *     summary="Check user subscription status",
     *     description="Returns whether the logged-in user has an active subscription. Requires Sanctum token.",
     *     tags={"Subscription"},
     *     security={{"sanctum":{}}},
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Subscription status",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="subscriptionActive", type="boolean", example=true)
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function checkSubscriptionForUser(Request $request)
    {
        $user = $request->user();

        $subscription = UserSubscription::where('user_id', $user->id)
                                    ->where('status', 'active')
                                    ->whereDate('end_date', '>=', Carbon::today())
                                    ->first();

        return response()->json([
            "status" => true,
            "subscriptionActive" => $subscription ? true : false,
        ]);
    }

}
