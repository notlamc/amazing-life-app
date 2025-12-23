<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\admin\NotificationTemplate;
use Yajra\DataTables\Facades\DataTables;

class NotificationTemplateController extends Controller
{
    public function notificationList()
    {
        return view('admin.notification.notification-list');
    }
    
    public function notificationCreate()
    {
        return view('admin.notification.notification-add');
    }

    public function notificationStore(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'description' => 'required',
            'type' => 'required',
            'subject' => 'required'            
        ]);
        $variables = '' ;
        // Convert comma-separated variables to array
        if ($request->variables) {
            $variables = json_encode(array_map('trim', explode(',', $request->variables)));
        }
        // 💾 Save in Database
        NotificationTemplate::create([
            'name' => $request->title,
            'type' => $request->type,
            'subject' => $request->subject,
            'body' => $request->description,
            'variables' => $variables,
            'status' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'notification created successfully!',
            'redirect' => route('superadmin.notification.list.page')
        ]);
    }



    public function getNotificationList(Request $request)
    {
        $video = NotificationTemplate::select('id', 'name', 'type')->orderBy('id', 'asc');
        return DataTables::of($video)->make(true);
    }

    public function deleteNotification(Request $request)
    {
        NotificationTemplate::find($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function updateNotificationStatus(Request $request)
    {
        $video = NotificationTemplate::find($request->id);
        $video->status = ($video->status === 'active') ? 'deactive' : 'active';
        $video->save();

        return response()->json(['success' => true]);
    }

    public function notificationEdit($id)
    {
        $video = NotificationTemplate::findOrFail($id);
        return view('admin.notification.notification-edit', compact('video'));
    }


    public function updateNotificationStore(Request $request, $id)
    {
        $video = NotificationTemplate::findOrFail($id);

        $request->validate([
            'title'  => 'required|string|max:255',
            'description' => 'required',
            'type' => 'required',
            
        ]);
        if ($request->variables) {
            $variables = json_encode(array_map('trim', explode(',', $request->variables)));
        }
        $updateData = [
            'name' => $request->title,
            'body' => $request->description,
            'type' => $request->type,
            'subject' => $request->subject,
            'variables' => $variables,
        ];

        
        // 🔹 Update only provided fields
        $video->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully!',
            'redirect' => route('superadmin.notification.list.page')
        ]);
    }


    public function getNotificationDetails(Request $request)
    {
        $video = NotificationTemplate::find($request->id);

        if (!$video) {
            return redirect()->back()->with('error', 'video not found.');
        }

        return view('admin.notification.view-notification', compact('video'));
    }

     public function view($id)
    {
        $notification = NotificationTemplate::findOrFail($id);
        return view('admin.notification.view-notification', compact('notification'));
    }
}