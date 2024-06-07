<?php

namespace App\Http\Controllers\Auth; 
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use DB; 
use Carbon\Carbon; 
use App\Models\User;
use App\Models\PasswordReset;
use Mail; 
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function showForgetPasswordForm()
    {
        return view('auth.forgetPassword');
    }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ], [
            'email.exists' => 'The provided email does not exist in our records.',
        ]);

        $token = Str::random(64);

        PasswordReset::create([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return back()->with('message', 'We have e-mailed your password reset link!');
    }
        /**
         * Write code on Method
         *
         * @return response()
         */
    public function showResetPasswordForm($token) {
        $userEmail = PasswordReset::where([
            'token' => $token
        ])->pluck('email')->first();
        return view('auth.forgetPasswordLink', compact('token', 'userEmail'));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = PasswordReset::where([
            'email' => $request->email,
            'token' => $request->token
        ])->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $request->email)->delete();

        Auth::login($user);

        if (Auth::user()->role_as == '1') 
        {
            return redirect('admin')->with('success', 'Your password has been changed!');
        } elseif (Auth::user()->role_as == '2')
        {
            return redirect('master')->with('success', 'Your password has been changed!');
        } elseif (Auth::user()->role_as == '3')
        {
            return redirect('user')->with('success', 'Your password has been changed!');
        }
    }
}
