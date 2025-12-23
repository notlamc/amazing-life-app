<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\OfflineDownload;
use Illuminate\Http\Request;

class OfflineDownloadController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/offline/save",
     *     summary="Save offline video metadata",
     *     description="Stores metadata for an offline downloaded video like YouTube",
     *     tags={"Offline Download"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"video_id", "device_file_id", "duration"},
     *             @OA\Property(property="video_id", type="integer", example=101),
     *             @OA\Property(property="device_file_id", type="string", example="file_234abc"),
     *             @OA\Property(property="duration", type="integer", example=620)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Offline video metadata saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Offline video metadata saved successfully"),
     *             @OA\Property(property="expires_at", type="string", example="2025-01-12 23:59:59"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="video_id", type="integer", example=101),
     *                 @OA\Property(property="device_file_id", type="string", example="file_234abc"),
     *                 @OA\Property(property="expires_at", type="string", example="2025-01-12 23:59:59")
     *             )
     *         )
     *     )
     * )
     */
    public function saveOfflineVideo(Request $request)
    {
        $request->validate([
            'video_id' => 'required|integer',
            'device_file_id' => 'required|string',
            'duration' => 'required|integer'
        ]);

        $userId = $request->user()->id;

        $expiry = now()->addDays(30);

        $download = OfflineDownload::updateOrCreate(
            [
                'user_id' => $userId,
                'video_id' => $request->video_id,
            ],
            [
                'device_file_id' => $request->device_file_id,
                'expires_at' => $expiry,
                'duration' => $request->duration
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Offline video metadata saved successfully',
            'expires_at' => $expiry,
            'data' => $download
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/offline/list",
     *     summary="List offline downloaded videos",
     *     description="Fetch all offline download records with video details",
     *     tags={"Offline Download"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Offline videos fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Offline videos fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="video_id", type="integer", example=101),
     *                     @OA\Property(property="device_file_id", type="string", example="file_234abc"),
     *                     @OA\Property(property="progress_percent", type="integer", example=75),
     *                     @OA\Property(property="expires_at", type="string", example="2025-02-05 23:59:59"),
     *                     @OA\Property(
     *                         property="video",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Morning Yoga"),
     *                         @OA\Property(property="thumbnail_path", type="string", example="thumb.jpg")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function listDownloads(Request $request)
    {
        $downloads = OfflineDownload::with('video')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Offline videos fetched successfully',
            'data' => $downloads
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/offline/remove",
     *     summary="Remove offline downloaded video",
     *     description="Delete the offline download entry for a specific video",
     *     tags={"Offline Download"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"video_id"},
     *             @OA\Property(property="video_id", type="integer", example=101)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Offline video removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Offline video removed successfully")
     *         )
     *     )
     * )
     */
    public function removeDownload(Request $request)
    {
        $request->validate([
            'video_id' => 'required|integer',
        ]);

        OfflineDownload::where('user_id', $request->user()->id)
            ->where('video_id', $request->video_id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Offline video removed successfully'
        ]);
    }


    /**
     * @OA\Put(
     *     path="/api/offline/sync-progress",
     *     summary="Sync offline playback progress",
     *     description="Sync watch progress for offline video playback",
     *     tags={"Offline Download"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"video_id", "last_watched_second", "duration"},
     *             @OA\Property(property="video_id", type="integer", example=101),
     *             @OA\Property(property="last_watched_second", type="integer", example=120),
     *             @OA\Property(property="duration", type="integer", example=600)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Progress synced successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Progress synced successfully")
     *         )
     *     )
     * )
     */
    public function syncOfflineProgress(Request $request)
    {
        $request->validate([
            'video_id' => 'required|integer',
            'last_watched_second' => 'required|integer',
            'duration' => 'required|integer'
        ]);

        $userId = $request->user()->id;

        $progress = ($request->last_watched_second / $request->duration) * 100;

        OfflineDownload::where('user_id', $userId)
            ->where('video_id', $request->video_id)
            ->update([
                'last_watched_second' => $request->last_watched_second,
                'duration' => $request->duration,
                'progress_percent' => $progress
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Progress synced successfully'
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/offline/status/{videoId}",
     *     summary="Get offline download status",
     *     description="Check if the video is downloaded offline and whether it is expired",
     *     tags={"Offline Download"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="videoId",
     *         in="path",
     *         required=true,
     *         description="Video ID",
     *         @OA\Schema(type="integer", example=101)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Offline status fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="is_downloaded", type="boolean", example=true),
     *             @OA\Property(property="expired", type="boolean", example=false),
     *             @OA\Property(property="expires_at", type="string", example="2025-01-20 23:59:59"),
     *             @OA\Property(property="progress", type="integer", example=75)
     *         )
     *     )
     * )
     */
    public function offlineStatus(Request $request, $videoId)
    {
        $record = OfflineDownload::where('user_id', $request->user()->id)
            ->where('video_id', $videoId)
            ->first();

        if (!$record) {
            return [
                'is_downloaded' => false
            ];
        }

        $isExpired = $record->expires_at && now()->greaterThan($record->expires_at);

        return [
            'is_downloaded' => !$isExpired,
            'expired' => $isExpired,
            'expires_at' => $record->expires_at,
            'progress' => $record->progress_percent
        ];
    }
}
