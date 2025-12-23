<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\ContinueWatching;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Continue Watching",
 *     description="API Endpoints for Continue Watching feature"
 * )
 */
class ContinueWatchingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/continue-watching/update",
     *     tags={"Continue Watching"},
     *     summary="Update or create video watching progress",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"video_id","last_watched_second","duration"},
     *             @OA\Property(property="video_id", type="integer", example=1),
     *             @OA\Property(property="last_watched_second", type="integer", example=120),
     *             @OA\Property(property="duration", type="integer", example=600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progress updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Progress updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="video_id", type="integer", example=1),
     *                 @OA\Property(property="last_watched_second", type="integer", example=120),
     *                 @OA\Property(property="duration", type="integer", example=600),
     *                 @OA\Property(property="progress_percent", type="number", example=20),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'video_id' => 'required|integer',
            'last_watched_second' => 'required|integer',
            'duration' => 'required|integer',
        ]);

        $userId = $request->user()->id;

        $progress = ($request->last_watched_second / $request->duration) * 100;

        $record = ContinueWatching::updateOrCreate(
            [
                'user_id' => $userId,
                'video_id' => $request->video_id
            ],
            [
                'last_watched_second' => $request->last_watched_second,
                'duration' => $request->duration,
                'progress_percent' => $progress
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Progress updated successfully',
            'data' => $record
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/continue-watching/get",
     *     tags={"Continue Watching"},
     *     summary="Get video watching progress for a user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="video_id",
     *         in="query",
     *         required=true,
     *         description="ID of the video",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Video progress fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="video_id", type="integer", example=1),
     *                 @OA\Property(property="last_watched_second", type="integer", example=120),
     *                 @OA\Property(property="duration", type="integer", example=600),
     *                 @OA\Property(property="progress_percent", type="number", example=20),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function getProgress(Request $request)
    {
        $record = ContinueWatching::where('user_id', $request->user()->id)
            ->where('video_id', $request->video_id)
            ->first();

        return response()->json([
            'status' => true,
            'data' => $record
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/continue-watching/list",
     *     tags={"Continue Watching"},
     *     summary="List all videos a user is currently watching",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of videos fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="video_id", type="integer", example=1),
     *                     @OA\Property(property="last_watched_second", type="integer", example=120),
     *                     @OA\Property(property="duration", type="integer", example=600),
     *                     @OA\Property(property="progress_percent", type="number", example=20),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function list(Request $request)
    {
        $records = ContinueWatching::where('user_id', $request->user()->id)
            ->orderBy('updated_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $records
        ]);
    }
}
