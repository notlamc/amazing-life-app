<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\admin\Video;
use Yajra\DataTables\Facades\DataTables;

class VideoController extends Controller
{
    public function videoList()
    {
        return view('admin.video.video-list');
    }
    
    public function videoCreate()
    {
        return view('admin.video.video-add');
    }

    // public function videoStore(Request $request)
    // {
    //     $request->validate([
    //         'title'  => 'required|string|max:255',
    //         'description' => 'required',
    //         'duration' => 'required',
    //         'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'video_file' => 'required|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm|max:51200', // 50MB
    //     ]);

    //     $thumbnailPath = null;
    //     $videoPath = null;

    //     // 🖼️ Handle Thumbnail Upload
    //     if ($request->hasFile('profile_image')) {
    //         $file = $request->file('profile_image');
    //         $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //         $destination = public_path('uploads/profile_images');

    //         if (!file_exists($destination)) {
    //             mkdir($destination, 0777, true);
    //         }

    //         $file->move($destination, $filename);
    //         $thumbnailPath = asset('uploads/profile_images/' . $filename);
    //     }

    //     // 🎥 Handle Video Upload
    //     if ($request->hasFile('video_file')) {
    //         $file = $request->file('video_file');
    //         $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //         $destination = public_path('uploads/videos');

    //         if (!file_exists($destination)) {
    //             mkdir($destination, 0777, true);
    //         }

    //         $file->move($destination, $filename);
    //         $videoPath = asset('uploads/videos/' . $filename);
    //     }

    //     // 💾 Save in Database
    //     Video::create([
    //         'title' => $request->title,
    //         'tags' => $request->tags,
    //         'metatags' => $request->metatags,
    //         'description' => $request->description,
    //         'duration' => $request->duration,
    //         'thumbnail_path' => $thumbnailPath,
    //         'video_path' => $videoPath,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Video created successfully!',
    //         'redirect' => route('superadmin.video.list.page')
    //     ]);
    // }
    public function videoStore(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'description' => 'required',
            'duration' => 'nullable',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video_file' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm|max:51200', // 50MB
            'categories'    => 'required|array|min:1',
            'categories.*'  => 'string|max:255',
        ]);
        $thumbnailPath = null;
        $videoPath = null;

        // 🖼️ Handle Thumbnail Upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/profile_images');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);
            $thumbnailPath = asset('uploads/profile_images/' . $filename);
        }

        // 🎥 Handle Video Upload
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/videos');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);
            $videoPath = asset('uploads/videos/' . $filename);
        }

        $video = Video::create([
            'title' => $request->title,
            'tags' => $request->tags,
            'metatags' => $request->metatags,
            'description' => $request->description,
            'categories' => is_array($request->categories) ? json_encode($request->categories) : $request->categories, 
            'duration' => $request->duration,
            'thumbnail_path' => $thumbnailPath,
            'video_path' => $videoPath,
        ]);

        $video->categories()->sync($request->categories);

        return response()->json([
            'success' => true,
            'message' => 'Video created successfully!',
            'redirect' => route('superadmin.video.list.page')
        ]);
    }



    public function getVideoList(Request $request)
    {
        // Select specific columns and eager load categories
        $videos = Video::select('id', 'title', 'thumbnail_path', 'status', 'views', 'likes', 'duration')
            ->with('categories:id,name') // eager load categories
            ->orderBy('id', 'desc')
            ->get();

        // Add categories_name property
        $video = $videos->map(function($video) {
            $video->categories_name = $video->categories->pluck('name')->join(', ');
            return $video;
        });
        return DataTables::of($video)->make(true);
    }

    public function deleteVideo(Request $request)
    {
        Video::find($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function updateVideoStatus(Request $request)
    {
        $video = Video::find($request->id);
        $video->status = ($video->status === 'active') ? 'deactive' : 'active';
        $video->save();

        return response()->json(['success' => true]);
    }

    public function videoEdit($id)
    {
        $video = Video::findOrFail($id);
        return view('admin.video.video-edit', compact('video'));
    }


    public function updateVideoStore(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'title'  => 'required|string|max:255',
            'description' => 'required',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'categories'    => 'required|array|min:1',
            'categories.*'  => 'string|max:255',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'metatags' => $request->metatags,
            'categories' => $request->categories,
        ];

        // 🖼️ If a new image is uploaded, handle it
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/profile_images');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);
            $path = asset('uploads/profile_images/' . $filename);

            // 🔹 Add only if a new image was uploaded
            $updateData['thumbnail_path'] = $path;
        }

        // 🔹 Update only provided fields
        $video->update($updateData);
         $video->categories()->sync($request->categories);

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully!',
            'redirect' => route('superadmin.video.list.page')
        ]);
    }


    public function getVideoDetails(Request $request)
    {
        $video = Video::with('videoCategories')->find($request->id);

        if (!$video) {
            return redirect()->back()->with('error', 'video not found.');
        }

        return view('admin.video.view-video', compact('video'));
    }
}
