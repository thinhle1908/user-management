<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\CrudHistory;
use App\Models\User;
use App\Models\User_Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function getAllUser(Request $request)
    {
        $users = User::all();
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
            'email' => 'required|email',
            'password' => 'required|min:6',
            'permission' => 'required'
        ]);
        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permission' => $request->permission,
            'created_user_id' => Auth::user()->id
        ]);

        return redirect(route('alluser'));
    }
    //
    public function deleteUser($id)
    {
        //Check admin

        $user = User::find($id)->delete();
        if (!$user) {
            return response()->json([
                'message' => 'User does not exist'
            ]);
        }

        return redirect(route('alluser'));
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
            'permission' => 'required',
        ]);
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permission' => $request->permission,
            'updated_user_id' => auth()->user()->id
        ]);

        CrudHistory::create([
            'user_id' => $id,
            'action' => 'update',
            'created_by_user' => $user->created_user_id,
            'updated_by_user' => auth()->user()->id
        ]);
        return redirect(route('alluser'));
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
}
