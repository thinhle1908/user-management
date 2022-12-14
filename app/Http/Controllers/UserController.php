<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Mail\SendMail;
use App\Models\User;
use App\Models\User_Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function getAllUser(Request $request)
    {
        $users = User::paginate(10);

        if($request->role){
            $users = User::where('role_id',$request->role)->where('banned',$request->banned)->where('email_verify',$request->email_verify)->paginate(10);
        }
        if($request->keyword){
            $users = User::where('name','like',$request->keyword.'%')->paginate(10);
        }
        if($request->sort_by){
            if($request->sort_by==1){
                $users = User::orderBy('id')->paginate(10);
            }
            else{
                $users = User::orderBy('id','desc')->paginate(10);
            }
           
        }
        return view('listuser')->with('users', $users);
    }
    //
    public function formAddUser()
    {
        return view('add');
    }
    public function addUser(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id'=>$request->role,
            'created_user_id' => Auth::user()->id
        ]);

        return redirect(route('alluser'))->withSuccess('Add User Successfully');
    }
    //
    public function deleteUser($id)
    {
        //Check admin

        $user = User::find($id)->delete();

        return redirect(route('alluser'))->withSuccess('Delete User Successfully');
    }
    //
    public function editUser(Request $request, $id)
    {
        //Check admin


        //Check user 
        $user = User::find($id);

        return view('edit')->with('user', $user);
    }
    public function updateUser(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role_id' => 'required',
        ]);
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'updated_user_id' => auth()->user()->id
        ]);

        return redirect(route('alluser'))->withSuccess('Edit User Successfully');
    }
    //
    public function searchUser(Request $request)
    {
        //Check admin

        $keyword = $request->input('keyword');
        $user = User::where('name', 'like', "%$keyword%")->orWhere('email', 'like', "%$keyword%")->get();

        return response()->json([
            'Users' => $user
        ]);
    }
    //
    public function filterUsers(Request $request)
    {
        //Check admin

        $validated = $request->validate([
            'email_verify' => 'required|boolean',
            'banned' => 'required|boolean'
        ]);
        $user = User::where('email_verify', $request->email_verify)->where('banned', $request->banned)->get();
        return $user;
    }
    //
    public function checkAdmin()
    {
        if (!Gate::allows('admin-only', auth()->user())) {
            return response()->json([
                'message' => 'You must be admin'
            ]);
        }
    }
    public function blockUser($id)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ]);
        }
        $user->update(['banned' => 1]);
        return redirect(route('alluser'));
    }
    public function unBlockUser($id)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ]);
        }
        $user->update(['banned' => 0]);
        return redirect(route('alluser'));
    }
    public function getProfile(Request $request)
    {
        $userProfile = User_Profile::where('user_id', Auth::user()->id)->first();

        return view('profile')->with('userProfile', $userProfile);
    }
    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_phone' => 'required',
            'address' => 'required',
            'country' => 'required',
        ]);
        $userProfile = User_Profile::where('user_id', Auth::user()->id)->first();
        if ($userProfile) {
            $userProfile->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile_phone' => $request->mobile_phone,
                'address' => $request->address,
                'country' => $request->country,
            ]);
        } else {
            User_Profile::create([
                'user_id'=>Auth::user()->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile_phone' => $request->mobile_phone,
                'address' => $request->address,
                'country' => $request->country,
            ]);
        }
        return redirect()->back()->withSuccess('Profile saved successfully');
    }
    public function getChangePassWord()
    {
        return view('changePassword');
    }
    public function postChangePassWord(Request $request)
    {
        $validated = $request->validate([
            'old_password' => 'required|min:6',
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password'=>'required|min:6'
        ]);
        if (!Hash::check($request->old_password,Auth::user()->password , [])){
            return Redirect::back()->withErrors(['msg' => 'Old password is incorrect']);
        }
        $user = User::find(Auth::user()->id);
        $user->update([
            'password'=>Hash::make($request->password)
        ]);
        return redirect()->back()->withSuccess('Change password successfully');
    }
    public function getSendMail($id)
    {
        $user = User::find($id);
        if(!$user){
            return Redirect::back()->withErrors(['msg' => 'User does not exist']);
        }
        return view('sendUserMail');
    }
    public function postSendMail(Request $request ,$id)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
        $user = User::find($id);
        
        $mailable = new SendMail($request->title,$request->content);
        try {
            Mail::to($user->email)->send($mailable);
        } catch (\Exception $ex) {
            return $ex;
        }
        return redirect(route('alluser'))->withSuccess('Email sent successfully');
       
    }
}
