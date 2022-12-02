<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function getInfo($social)
    {
        if ($social == "google" || $social == "github" || $social == "facebook") {
            return Socialite::driver($social)->redirect();
        } else {
            return response()->json([
                'message' => 'Social Unsupported'
            ], 404);
        }
    }
    public function checkInfo($social, Request $request)
    {
        if ($social == "google") {
            $info = Socialite::driver('google')->stateless()->user();
        } else {
            $info = Socialite::driver($social)->user();
        }
        //Check Email and Create User
        $user = User::where('email', $info->email)->first();

        if (!$user) {
            //Create User
            $user = User::create([
                'name' => $info->name,
                'email' => $info->email,
                'password' => null,
                'social' => $social,
                'email_verify' => 1,
                'actived_at' => date('Y-m-d H:i:s'),
            ]);
            //--Create User--
        }
        // //--Check Email--
        $user->update([
            'last_login' => date('Y-m-d H:i:s'),
            'status' => 1
        ]);


       

         Auth::login($user);

        return redirect()->intended('/');
        // //--Create Bearer Token--


    }
}
