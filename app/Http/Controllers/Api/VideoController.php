<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Category;
use App\Models\Admin\Video;
use App\Models\Admin\ContinueWatching;
use App\Models\Admin\OfflineDownload;
use Illuminate\Http\Request;
use DB;
class VideoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/video/categories",
     *     summary="Get all video categories",
     *     description="Returns a list of all video categories",
     *     tags={"Video"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Video categories fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Video categories fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Yoga"),
     *                     @OA\Property(property="image", type="string", example="yoga.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function categories()
    {
        $categories = Category::select('id', 'name', 'profile_image')->get();

        return response()->json([
            "status" => true,
            "message" => "Video categories fetched successfully",
            "data" => $categories
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/video/category/{id}/videos",
     *     summary="Get videos by category",
     *     description="Returns all videos under a specific category",
     *     tags={"Video"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Videos fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Videos fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=101),
     *                     @OA\Property(property="title", type="string", example="Morning Yoga"),
     *                     @OA\Property(property="thumbnail", type="string", example="thumb1.jpg"),
     *                     @OA\Property(property="video_url", type="string", example="video1.mp4"),
     *                     @OA\Property(property="duration", type="string", example="12:45"),
     *                     @OA\Property(property="description", type="string", example="Relaxing yoga session")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function videosByCategory(Request $request,$categoryId)
    {
        $userId = $request->user()->id;
        $videos = Video::join('video_category', 'videos.id', '=', 'video_category.video_id')
            ->join('categories', 'video_category.category_id', '=', 'categories.id')
            ->where('categories.id', $categoryId) // filter by category
            ->select(
                'videos.id',
                'videos.title',
                'videos.thumbnail_path',
                'videos.video_path',
                'videos.duration',
                'videos.description',
                'categories.name as category_name'
            )
            ->get();
        // Add continue watching details for each video
        $videos->transform(function ($video) use ($userId) {
            $continueWatching = ContinueWatching::where('user_id', $userId)
                ->where('video_id', $video->id)
                ->first();

            $video->continue_watching = $continueWatching ? [
                'last_watched_second' => $continueWatching->last_watched_second,
                'duration' => $continueWatching->duration,
                'progress_percent' => $continueWatching->progress_percent
            ] : null;

            return $video;
        });
        
        return response()->json([
            "status" => true,
            "message" => "Videos fetched successfully",
            "categoryName"=> $videos[0]['category_name'],
            "data" => $videos
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/video/categories-with-videos/{categoryId}",
     *     summary="Get categories with videos",
     *     description="Returns all categories or a specific category along with their videos",
     *     tags={"Video"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=false,
     *         description="Optional category ID to filter videos",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories and videos fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories and videos fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Yoga"),
     *                     @OA\Property(property="image", type="string", example="yoga.jpg"),
     *                     @OA\Property(
     *                         property="videos",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=101),
     *                             @OA\Property(property="title", type="string", example="Morning Yoga"),
     *                             @OA\Property(property="thumbnail_path", type="string", example="thumb1.jpg"),
     *                             @OA\Property(property="video_path", type="string", example="video1.mp4"),
     *                             @OA\Property(property="duration", type="string", example="12:45")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function categoriesWithVideos(Request $request, $categoryId = 0)
    {
        $userId = $request->user()->id;
        $query = Category::with([
            'videos:id,title,thumbnail_path,video_path,duration'
        ]);

        if ($categoryId > 0) {
            $query->where('id', $categoryId);
        }

        $categories = $query->get();

       // Attach continue watching data to each video
        $categories->transform(function ($category) use ($userId) {
            $category->videos->transform(function ($video) use ($userId) {
                $continueWatching = ContinueWatching::where('user_id', $userId)
                    ->where('video_id', $video->id)
                    ->first();

                $video->continue_watching = $continueWatching ? [
                    'last_watched_second' => $continueWatching->last_watched_second,
                    'duration' => $continueWatching->duration,
                    'progress_percent' => $continueWatching->progress_percent
                ] : null;

                return $video;
            });

            return $category;
        });

        return response()->json([
            "status" => true,
            "message" => "Categories and videos fetched successfully",
            "data" => $categories
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/video/{videoId}",
     *     summary="Get video details",
     *     description="Returns details of a specific video by ID",
     *     tags={"Video"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="videoId",
     *         in="path",
     *         required=true,
     *         description="ID of the video to fetch",
     *         @OA\Schema(type="integer", example=101)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Video details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Video details fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=101),
     *                 @OA\Property(property="title", type="string", example="Morning Yoga"),
     *                 @OA\Property(property="description", type="string", example="A relaxing morning yoga session"),
     *                 @OA\Property(property="thumbnail_path", type="string", example="thumb1.jpg"),
     *                 @OA\Property(property="video_path", type="string", example="video1.mp4"),
     *                 @OA\Property(property="duration", type="string", example="12:45"),
     *                 @OA\Property(property="category_id", type="integer", example=3),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Video not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getVideoDetails(Request $request,$videoId)
    {
        $video = Video::find($videoId);

        if (!$video) {
            return response()->json([
                "status" => false,
                "message" => "Video not found",
                "data" => null
            ], 404);
        }

        // Fetch continue watching record for the user
        $continueWatching = ContinueWatching::where('user_id', $request->user()->id)
            ->where('video_id', $videoId)
            ->first();

        // Attach continue watching details to the video response
        $video->continue_watching = $continueWatching ? [
            "last_watched_second" => $continueWatching->last_watched_second,
            "duration" => $continueWatching->duration,
            "progress_percent" => $continueWatching->progress_percent
        ] : null;

        $offlineDownload = OfflineDownload::where('user_id', $request->user()->id)
            ->where('video_id', $videoId)
            ->first();

        $video->offline_download = $offlineDownload ? [
            'is_downloaded' => true,
            'local_file_path' => $offlineDownload->local_file_path,
            'downloaded_at' => $offlineDownload->downloaded_at
        ] : [
            'is_downloaded' => false
        ];


        return response()->json([
            "status" => true,
            "message" => "Video details fetched successfully",
            "data" => $video
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/videos/trending",
     *     summary="Get trending videos based on likes",
     *     description="Returns a list of trending videos ordered by likes, including continue watching info",
     *     tags={"Video"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Limit the number of trending videos",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trending videos fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trending videos fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=101),
     *                     @OA\Property(property="title", type="string", example="Morning Yoga"),
     *                     @OA\Property(property="description", type="string", example="A relaxing morning yoga session"),
     *                     @OA\Property(property="thumbnail_path", type="string", example="thumb1.jpg"),
     *                     @OA\Property(property="video_path", type="string", example="video1.mp4"),
     *                     @OA\Property(property="duration", type="string", example="12:45"),
     *                     @OA\Property(property="likes", type="integer", example=150),
     *                     @OA\Property(property="continue_watching", type="object",
     *                         @OA\Property(property="last_watched_second", type="integer", example=120),
     *                         @OA\Property(property="duration", type="integer", example=600),
     *                         @OA\Property(property="progress_percent", type="number", example=20)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function trendingVideos(Request $request)
    {
        $limit = $request->query('limit', 10);
        $userId = $request->user()->id;

        // Fetch trending videos ordered by likes
        $videos = Video::orderByDesc('likes')->take($limit)->get();

        // Attach continue watching info for each video
        $videos->transform(function ($video) use ($userId) {
            $continueWatching = ContinueWatching::where('user_id', $userId)
                ->where('video_id', $video->id)
                ->first();

            $video->continue_watching = $continueWatching ? [
                'last_watched_second' => $continueWatching->last_watched_second,
                'duration' => $continueWatching->duration,
                'progress_percent' => $continueWatching->progress_percent
            ] : null;

            $offline = OfflineDownload::where('user_id', $request->user()->id)
                ->where('video_id', $videoId)
                ->first();

            $video->offline = [
                'is_downloaded' => $offline && now()->lessThan($offline->expires_at),
                'expires_at' => $offline->expires_at ?? null,
                'progress' => $offline->progress_percent ?? 0
            ];
            return $video;
        });

        return response()->json([
            'status' => true,
            'message' => 'Trending videos fetched successfully',
            'data' => $videos
        ]);
    }


}