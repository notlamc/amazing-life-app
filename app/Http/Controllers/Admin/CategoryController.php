<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\admin\Category;
use App\Models\admin\Agent;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function categoryList()
    {
        return view('admin.category.category-list');
    }
    
    public function categoryCreate()
    {
        return view('admin.category.category-add');
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = null;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/profile_images'); 

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $path = asset('uploads/profile_images/' . $filename);
        }

        Category::create([
            'name' => $request->name,
            'profile_image' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
            'redirect' => route('superadmin.category.list.page')
        ]);
    }


    public function getCategoryList(Request $request)
    {
        $agents = Category::select('id', 'name', 'profile_image', 'status')->orderBy('id', 'desc');
        return DataTables::of($agents)->make(true);
    }

    public function deleteCategory(Request $request)
    {
        Category::find($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function updateCategoryStatus(Request $request)
    {
        $agent = Category::find($request->id);
        $agent->status = ($agent->status === 'active') ? 'deactive' : 'active';
        $agent->save();

        return response()->json(['success' => true]);
    }

    public function categoryEdit($id)
    {
        $agent = Category::findOrFail($id);
        return view('admin.category.category-edit', compact('agent'));
    }


    public function updateCategoryStore(Request $request, $id)
    {
        $agent = Category::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $agent->profile_image; 

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/profile_images');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $path = asset('uploads/profile_images/' . $filename);
        }

        $agent->update([
            'name' => $request->name,
            'profile_image' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'category updated successfully!',
            'redirect' => route('superadmin.category.list.page')
        ]);
    }

    public function getCategoryDetails(Request $request)
    {
        $agent = Category::find($request->id);

        if (!$agent) {
            return redirect()->back()->with('error', 'category not found.');
        }

        return view('admin.category.view-category', compact('agent'));
    }

    //  Check if email exists
    public function checkEmail(Request $request)
    {
        $exists = Admin::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    // Store email in session
    public function setResetEmail(Request $request)
    {
        session(['reset_email' => $request->email]);
        return response()->json(['success' => true]);
    }

    // Show Reset Password Page
    public function viewResetPasswordPage()
    {
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('')->withErrors(['email' => 'Please enter your email first.']);
        }

        return view('admin.reset-password', compact('email'));
    }

    //  Reset Password
    public function resetPasswordSubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please try again.']);
        }

        $admin = Admin::where('email', $email)->first();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Invalid email address.']);
        }

        $admin->password = Hash::make($request->password);
        $admin->pass = $request->password;
        $admin->save();

        session()->forget('reset_email');

        return response()->json(['success' => true, 'message' => 'Password change successfully!']);

    }

    
    public function callReports()
    {
        return view('admin.call-report.list');
    }

    public function pricingLists()
    {
        return view('admin.control-pricing.list');
    }

    public function pricingAdd()
    {
        return view('admin.control-pricing.add');
    }

}
