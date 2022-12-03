<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Mail\SendOtpEmail;
use App\Models\ResetPassWord;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class SendMailController extends Controller
{
    public function postResetPassword(Request $request, $token)
    {
        $validated = $request->validate([
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password' => 'required|min:6'
        ]);
        $resetPassword = ResetPassWord::where('token', $token)->latest()->first();

        $now = Carbon::now();

        if (!$token || $resetPassword->token != $token || $now->isAfter(($resetPassword->expire_at))) {
            return redirect(route('get.forgotpassword'))->withErrors([
                'message' => 'Email or password is incorrect, expired'
            ]);
        }
        $user = User::where('email', $resetPassword->email);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect(route('login.get'))->withSuccess(
            'Email or password is incorrect, expired'
        );
    }
    public function resetPassword($token)
    {
        $resetPassword = ResetPassWord::where('token', $token)->latest()->first();

        $now = Carbon::now();

        if (!$token || $resetPassword->token != $token || $now->isAfter(($resetPassword->expire_at))) {
            return redirect(route('get.forgotpassword'))->withErrors([
                'message' => 'Email or password is incorrect, expired'
            ]);
        }
        return view('resetPassword');
    }
    public function sendTokenResetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email does not exist'
            ]);
        }

        $resetPassword = ResetPassWord::create([
            'email' => $request->email,
            'token' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(10),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $token = $resetPassword->token;

        $mailable = new SendOtpEmail($token);

        //Send Mail
        try {
            Mail::to($request->email)->send($mailable);
            return response()->json(['Send otp code successfully']);
        } catch (\Exception $th) {
            return response()->json(['Failed to send otp code']);
        }

        return response()->json([
            'message' => 'Check your email'
        ]);
    }
    public function verifyEmail(Request $request, $token)
    {
        //Check email already exists verify
        $user = User::where('id', Auth()->user()->id)->first();

        $now = Carbon::now();

        $verificationCode = Verification::where('user_id', Auth()->user()->id)->latest()->first();
        if ($now->isAfter(($verificationCode->expire_at))) {
            return redirect(route('sendVerifyEmail.get'))->withErrors(
                'message',
                'Incorrect otp code'
            );
        }

        if ($verificationCode->token == $token);
        $user->update([
            'email_verify' => 1
        ]);
        return redirect(route('sendVerifyEmail.get'));
    }
    public function getsendCodeVerifyEmail()
    {
        return view('sendVerifyEmail');
    }
    public function postsendCodeVerifyEmail()
    {
        //Check email already exists verify
        $user = User::where('id', Auth()->user()->id)->first();

        if ($user->email_verify == '1') {
            return response()->json([
                'message' => 'Verified email'
            ]);
        }
        #User Does not have existing otp
        $verificationCode = Verification::where('user_id', Auth()->user()->id)->latest()->first();

        $now = Carbon::now();

        // if ($verificationCode && $now->isBefore(($verificationCode->expire_at))) {
        //     return $verificationCode;
        // }

        //Create a new otp
        $verify =  Verification::create([
            'user_id' => Auth()->user()->id,
            'otp' =>  Str::random(10),
            'expire_at' => Carbon::now()->addMinutes(10),
        ]);
        $otp = $verify->otp;
        $mailable = new SendOtpEmail($otp);
        try {
            Mail::to(Auth()->user()->email)->send($mailable);
        } catch (\Exception $ex) {
            return $ex;
        }
        return redirect()->back()->withSuccess('Email sent successfully');
    }

    public function sendMail(Request $request)
    {

        if (!Gate::allows('admin-only', auth()->user())) {
            return response()->json([
                'message' => 'You must be admin'
            ]);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'content' => 'required'
        ]);
        $data = $request->content;

        $mailable = new SendOtpEmail($data);
        try {
            Mail::to($request->email)->send($mailable);
            return response()->json(['Greate check your mail box']);
        } catch (\Exception $th) {
            return response()->json(['Unable to send this email']);
        }

        return response()->json([
            'message' => 'successfully'
        ]);
    }
    public function getForgotPassword()
    {

        return view('forgotPassword');
    }
    public function postForgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors([
                'message' => 'Email does not exist'
            ]);
        }

        $resetPassword = ResetPassWord::create([
            'email' => $request->email,
            'token' => Str::random(10),
            'expire_at' => Carbon::now()->addMinutes(10),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $token = $resetPassword->token;

        $mailable = new ForgotPassword($token);

        //Send Mail
        try {
            Mail::to($request->email)->send($mailable);
            return redirect()->back()->withSuccess('Send otp code successfully');
        } catch (\Exception $th) {
            return redirect()->back()->withErrors(['Failed to send otp code']);
        }
    }
}
