<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\admin\Admin;
use App\Models\admin\User;
use App\Models\admin\Agent;
use App\Models\admin\WalletTransaction;
use App\Models\admin\Wallet;
use App\Models\admin\UserSubscription ;

use Yajra\DataTables\Facades\DataTables;

use App\Http\Controllers\Controller;

use App\Models\admin\SiteSetting;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Schema;           // <-- important


class AdminController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'status'   => 'active'
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->get('remember'))) {
            $admin = Auth::guard('admin')->user();

            Log::info('Admin login successful', [
                'email' => $admin->email,
                'name'  => $admin->name,
                'role'  => $admin->role,
                'time'  => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Login successful'
            ]);
        } else {
            Log::warning('Admin login failed', [
                'email' => $request->email,
                'time'  => now()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ]);
        }
    }

    public function dashboard(){
        return view('admin.dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

   
    public function userList()
    {
        return view('admin.user.user-list');
    }

    // public function getUserList(Request $request)
    // {
    //     $users = User::with('referrals')->with('wallet')->with('referrals.subscriptions')->select('id', 'name', 'phone_number','referral_code', 'status');
    
    //     return DataTables::of($users)
    //      ->editColumn('phone_number', function($user) {
    //         // Replace phone_number with wallet balance
    //         return $user->wallet ? $user->wallet->balance : 0 ;
    //     })->addColumn('affiliate', function($user) {
    //         // Example: show user name inside button with badge
    //         $referrals = count($user->subscriptions) ?? 0;
    //         $total = count($user->referrals) ?? 0;; // replace with real total if needed
    //         $withSubscription = $user->referrals->filter(function ($ref) {
    //             return $ref->subscriptions->count() > 0;
    //         })->count();
            

    //         return '<button type="button" class="btn btn-secondarys position-relative">'
    //             . $withSubscription . '/' . $total .
    //            '</button>';
    //     })
    //     ->rawColumns(['affiliate'])
    //     ->make(true);
    // }

    public function getUserList(Request $request)
    {
        $users = User::with(['referrals', 'wallet', 'subscriptions'])
                    ->select('id', 'name', 'phone_number','referral_code', 'status');

        return DataTables::of($users)
            ->editColumn('phone_number', function($user) {
                return $user->wallet ? $user->wallet->balance : 0;
            })
            ->addColumn('affiliate', function($user) {
                $referrals = count($user->subscriptions) ?? 0;
                $total = count($user->referrals) ?? 0;
                $withSubscription = $user->referrals->filter(function ($ref) {
                    return $ref->subscriptions->count() > 0;
                })->count();

                return '<button type="button" class="btn btn-secondarys position-relative">'
                    . $withSubscription . '/' . $total .
                '</button>';
            })
            ->addColumn('extend_date', function($user) {
                $sub = $user->subscriptions->first(function($s){ return $s->is_active == 1 || $s->status === 'active'; });
                if (!$sub) {
                    $sub = $user->subscriptions->sortByDesc('created_at')->first();
                }
                return $sub ? ($sub->extend_date ?? $sub->end_date ?? null) : null;
            })
            ->addColumn('end_date', function($user) {
                $sub = $user->subscriptions->first(function($s){ return $s->is_active == 1 || $s->status === 'active'; });
                if (!$sub) {
                    $sub = $user->subscriptions->sortByDesc('created_at')->first();
                }
                return $sub ? ($sub->end_date ?? null) : null;
            })
            ->addColumn('subscription_record_id', function($user) {
                $sub = $user->subscriptions->first(function($s){ return $s->is_active == 1 || $s->status === 'active'; });
                if (!$sub) {
                    $sub = $user->subscriptions->sortByDesc('created_at')->first();
                }
                return $sub ? $sub->id : null;
            })

            ->rawColumns(['affiliate'])
            ->make(true);
    }


    public function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        $user->status = ($user->status === 'active') ? 'deactive' : 'active';
        $user->save();

        return response()->json(['success' => true]);
    }


    public function deleteUser(Request $request)
    {
        User::find($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function getUserDetails(Request $request)
    {
        $user = User::find($request->id);
        if(!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function agentList()
    {
        return view('admin.agent.agent-list');
    }
    
    public function agentCreate()
    {
        return view('admin.agent.agent-add');
    }

    public function agentStore(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email',
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

        Agent::create([
            'name' => $request->name,
            'email' => $request->email,
            'profile_image' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent created successfully!',
            'redirect' => route('admin.agent.list.page')
        ]);
    }


    public function getAgentList(Request $request)
    {
        $agents = Agent::select('id', 'name', 'profile_image', 'status')->orderBy('id', 'desc');
        return DataTables::of($agents)->make(true);
    }

    public function deleteAgent(Request $request)
    {
        Agent::find($request->id)->delete();
        return response()->json(['success' => true]);
    }

    public function updateAgentStatus(Request $request)
    {
        $agent = Agent::find($request->id);
        $agent->status = ($agent->status === 'active') ? 'deactive' : 'active';
        $agent->save();

        return response()->json(['success' => true]);
    }

    public function agentEdit($id)
    {
        $agent = Agent::findOrFail($id);
        return view('admin.agent.agent-edit', compact('agent'));
    }


    public function updateAgentStore(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email,' . $id,
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
            'email' => $request->email,
            'profile_image' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent updated successfully!',
            'redirect' => route('admin.agent.list.page')
        ]);
    }

    public function getAgentDetails(Request $request)
    {
        $agent = Agent::find($request->id);

        if (!$agent) {
            return redirect()->back()->with('error', 'Agent not found.');
        }

        return view('admin.agent.view-agent', compact('agent'));
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

    public function userTransactionsView($id)
    {
        $currentBalence = Wallet::where('user_id', $id)->first();  
        return view('admin.user.user-transactions', compact('id','currentBalence'));
    }

    public function userTransactionsData($id)
    {
        $transactions = WalletTransaction::where('user_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['data' => $transactions]);
    }

    public function userSubscriptionsView($id)
    {
        $currentBalence = Wallet::where('user_id', $id)->first();  
        return view('admin.user.user-subscriptions', compact('id','currentBalence'));
    }

    public function userSubscriptionsData($id)
    {
        $transactions = UserSubscription::where('user_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['data' => $transactions]);
    }

    public function settingPage()
    {
       $setting = SiteSetting::first();

        if (!$setting) {
            $setting = SiteSetting::create([
                'site_title' => '',
                'logo' => null,
                'favicon' => null,
                'referral_commission' => 0,
                'extend_duration_days' => 0,
            ]);
        }

        return view('admin.setting.setting-add', compact('setting'));
    }

    public function siteSettingsUpdate(Request $request)
    {
        $setting = SiteSetting::first();
        if (!$setting) {
            $setting = new SiteSetting();
        }

        $rules = [
            'site_title' => 'required|max:255',
            'referral_commission' => 'required|numeric|min:0',
            'extend_duration_days' => 'required|integer|min:0',
            'logo' => $setting->logo ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048' : 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'small_logo' => $setting->small_logo ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048' : 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'favicon' => $setting->favicon ? 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:1024' : 'required|image|mimes:jpg,jpeg,png,ico,webp|max:1024',
        ];

        $validated = $request->validate($rules);

        $dest = public_path('uploads/site_setting');
        if (!file_exists($dest)) mkdir($dest, 0777, true);

        if ($request->hasFile('logo')) {
            if ($setting->logo && file_exists(public_path($setting->logo))) @unlink(public_path($setting->logo));
            $f = $request->file('logo');
            $name = time().'_'.uniqid().'.'.$f->getClientOriginalExtension();
            $f->move($dest, $name);
            $setting->logo = 'uploads/site_setting/'.$name;
        }

        if ($request->hasFile('small_logo')) {
            if ($setting->small_logo && file_exists(public_path($setting->small_logo))) {
                @unlink(public_path($setting->small_logo));
            }
            $f = $request->file('small_logo');
            $name = time().'_'.uniqid().'.'.$f->getClientOriginalExtension();
            $f->move($dest, $name);
            $setting->small_logo = url('uploads/site_setting/'.$name);
        }


        if ($request->hasFile('favicon')) {
            if ($setting->favicon && file_exists(public_path($setting->favicon))) @unlink(public_path($setting->favicon));
            $f = $request->file('favicon');
            $name = time().'_'.uniqid().'.'.$f->getClientOriginalExtension();
            $f->move($dest, $name);
            $setting->favicon = 'uploads/site_setting/'.$name;
        }

        $setting->site_title = $request->site_title;
        $setting->referral_commission = $request->referral_commission;
        $setting->extend_duration_days = $request->extend_duration_days;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.',
            'redirect' => route('superadmin.setting')
        ]);
    }



    public function viewUserDetails($id)
    {
        $user = User::find($id);

        if(!$user){
            return redirect()->back()->with('error', 'User not found');
        }

        return view('admin.user.view-details', compact('user'));
    }



    public function extendSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer'
        ]);

        $sub = UserSubscription::find($request->subscription_id);
        if (!$sub) {
            return response()->json(['success' => false, 'message' => 'Subscription not found'], 404);
        }

        // get site settings (extend days)
        $setting = SiteSetting::first();
        $extendDays = intval($setting->extend_duration_days ?? 0);
        if ($extendDays <= 0) {
            return response()->json(['success' => false, 'message' => 'Extend duration not configured'], 400);
        }

        try {
            DB::beginTransaction();

            $oldEnd = $sub->end_date ? Carbon::parse($sub->end_date) : null;
            $today = Carbon::today();

            if ($oldEnd && $oldEnd->gte($today)) {
                $newStart = $oldEnd->copy()->addDay();
            } else {
                $newStart = $today;
            }

            // inclusive end: start + extendDays - 1
            $newEnd = $newStart->copy()->addDays($extendDays)->subDay();

            // Create new subscription row (match DB types: DATE fields)
            $newSub = new UserSubscription();
            $newSub->user_id = $sub->user_id;
            $newSub->subscription_id = $sub->subscription_id;
            $newSub->start_date = $newStart->toDateString();
            $newSub->end_date = $newEnd->toDateString(); 
            $newSub->status = $sub->status ?? 'pending';

            $newSub->is_active = 1;

            $newSub->payment_reference = 'admin_reference';

            if (Schema::hasColumn($newSub->getTable(), 'transaction_id')) {
                $newSub->transaction_id = $sub->transaction_id ?? 'Trans78636474847';
            }

            if (Schema::hasColumn($newSub->getTable(), 'payment_id')) {
                $newSub->payment_id = $sub->payment_id ?? 0;
            }

            $newSub->amount = $sub->amount ?? 0;
            $newSub->save();

            if ($sub->id !== $newSub->id && $sub->is_active) {
                $sub->is_active = 0;

                $sub->status = 'success';

                if (Schema::hasColumn($sub->getTable(), 'expired_at')) {
                    $sub->expired_at = $oldEnd ? $oldEnd->toDateString() : Carbon::today()->toDateString();
                }

                $sub->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Subscription extended by {$extendDays} days.",
                'new_subscription' => [
                    'id' => $newSub->id,
                    'user_id' => $newSub->user_id,
                    'start_date' => $newSub->start_date,
                    'end_date' => $newSub->end_date,
                    'payment_reference' => $newSub->payment_reference,
                    'payment_id' => $newSub->payment_id,
                    'transaction_id' => $newSub->transaction_id,
                    'status' => $newSub->status,
                    'is_active' => $newSub->is_active,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Extend subscription error: '.$e->getMessage().' -- '.$e->getTraceAsString());

            if (config('app.debug')) {
                return response()->json(['success' => false, 'message' => 'Failed to extend subscription: '.$e->getMessage()], 500);
            }

            return response()->json(['success' => false, 'message' => 'Failed to extend subscription'], 500);
        }
    }






}
