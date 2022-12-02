<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    // public function logout()
    // {
    //     // Revoke all tokens...
    //     auth()->user()->tokens()->delete();

    //     // Revoke the token that was used to authenticate the current request...
    //     // $request->user()->currentAccessToken()->delete();

    //     // // Revoke a specific token...
    //     // $user->tokens()->where('id', $tokenId)->delete();
    //     return response()->json([
    //         'message'=>'Logout Success'
    //     ]);
    // }

    // public function user(Request $request)
    // {
    //     return $request->user();
    // }

    // public function login(Request $request)
    // {
    //     //Validate 
    //     $message = [
    //         'email.email' => "Please enter correct email format",
    //         'email.required' => "Please enter the email",
    //         'password.required' => "Please enter the password",
    //     ];

    //     $validate = FacadesValidator::make($request->all(), [
    //         'email' => 'email|required',
    //         'password' => 'required'
    //     ], $message);

    //     if ($validate->fails()) {
    //         return response()->json(
    //             [
    //                 'message' => $validate->errors()
    //             ],
    //             403
    //         );
    //     }
    //     //--Validate--

    //     // Check Email
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password, [])) {
    //         return response()->json([
    //             'message' => "Email or password is incorrect"
    //         ], 404);
    //     }
    //     //--Check Email--

    //     //Create Bearer Token
    //     $token = $user->createToken('authToken')->plainTextToken;
    //     //--Create Bearer Token--

    //     //Return Bearer Token
    //     return response()->json([
    //         'access_token' => $token,
    //         'type_token' => 'Bearer'
    //     ], 200);
    //     //--Return Bearer Token--

    // }
    // public function register(Request $request)
    // {
    //     //Validate 
    //     $message = [
    //         'email.email' => "Please enter correct email format",
    //         'email.required' => "Please enter the email",
    //         'password.required' => "Please enter the password",
    //     ];

    //     $validate = FacadesValidator::make($request->all(), [
    //         'email' => 'email|required|unique:users',
    //         'password' => 'required'
    //     ], $message);

    //     if ($validate->fails()) {
    //         return response()->json(
    //             [
    //                 'message' => $validate->errors()
    //             ],
    //             404
    //         );
    //     }
    //     //--Validate--

    //     //Create User
    //     User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     //--Create User--

    //     //Return Message
    //     return response()->json([
    //         'message' => "Created",

    //     ], 200);
    //     //--Return Message--
    // 

    public function logout()
    {
        Auth::logout();
        return redirect(route('login.get'));
    }
    public function getRegister(Request $request)
    {
        return view('register');
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|same:password_confirm',
            'password_confirm' => 'required|min:6',
        ]);
        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return redirect('/login')->withSuccess('
        Successful registration please login');
    }
    public function getLogin(Request $request)
    {
        return view('login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
