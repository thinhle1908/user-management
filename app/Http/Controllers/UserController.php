<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\CrudHistory;
use App\Models\User;
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
        return view('listuser')->with('users',$users);
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
            'permission'=>'required'
        ]);
       $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permission'=>$request->permission,
            'created_user_id' => Auth::user()->id
        ]);

        //CRUD Hisotry
        CrudHistory::create([
            'user_id'=>$user->id,
            'action'=>'create',
            'created_by_user'=>Auth::user()->id,
            'updated_by_user'=>Auth::user()->id
        ]);

        return redirect(route('alluser'));
    }
    //
    public function deleteUser($id)
    {
        //Check admin
       
         $user = User::find($id)->delete();
         if(!$user){
            return response()->json([
                'message'=>'User does not exist'
            ]);
         }
         //CRUD Hisotry
         CrudHistory::create([
            'user_id'=>$id,
            'action'=>'delete',
            'updated_by_user'=>auth()->user()->id
        ]);
        return redirect( route('alluser'));
    }
    //
    public function editUser(Request $request,$id)
    {
        //Check admin
       

        //Check user 
        $user = User::find($id);
        
        return view('edit')->with('user',$user);
        
    }
    public function updateUser(Request $request,$id)
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
            'permission'=>$request->permission,
            'updated_user_id' => auth()->user()->id
        ]);

        CrudHistory::create([
            'user_id'=>$id,
            'action'=>'update',
            'created_by_user'=>$user->created_user_id,
            'updated_by_user'=>auth()->user()->id
        ]);
        return redirect( route('alluser'));
    }
    //
    public function searchUser(Request $request)
    {
        //Check admin
       
        $keyword = $request->input('keyword');
        $user = User::where('name','like',"%$keyword%")->orWhere('email', 'like', "%$keyword%")->get();

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
        $user = User::where('email_verify',$request->email_verify)->where('banned',$request->banned)->get();
        return $user ;
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
         if(!$user){
            return response()->json([
                'message'=>'User does not exist'
            ]);
         }
         $user->update(['banned'=>1]);
         //CRUD Hisotry
         CrudHistory::create([
            'user_id'=>$id,
            'action'=>'block',
            'updated_by_user'=>auth()->user()->id
        ]);
        return redirect( route('alluser'));
    }
    public function unBlockUser($id)
    {
       
         $user = User::find($id);
         if(!$user){
            return response()->json([
                'message'=>'User does not exist'
            ]);
         }
         $user->update(['banned'=>0]);
         //CRUD Hisotry
         CrudHistory::create([
            'user_id'=>$id,
            'action'=>'unblock',
            'updated_by_user'=>auth()->user()->id
        ]);
        return redirect( route('alluser'));
    }
}
