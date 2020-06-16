<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserForgotPassword;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;


class ForgotPasswordController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        if($user)
        {
            if($user->status == true)
            {
                $token = bcrypt($user->email);
                $forgotpassword = new UserForgotPassword();
                $forgotpassword->user_id = $user->id;
                $forgotpassword->token = $token;
                // dd($forgotpassword);
                $forgotpassword->save();
    
                $data = array(
                    'id' => $forgotpassword->id,
                    'name' => $user->name,
                    'token' => $token
                );
                Mail::to($user->email)->send(
                    new ForgotPasswordMail($data, 'Reset Password')
                );
                return 'true';
            } 
            else{
                return 'false';
            }
        }
        else{
            return 'false';
        }
    }

    public function changePass(){
        session_start();
        $forgetPass = UserForgotPassword::where('id',request('id'))->first();
        if($forgetPass){
            $_SESSION['forgetPass'] = $forgetPass;
            if($forgetPass->token == request('token')){
                return view('reset-password');
            }else{
                return view('problem');
            }
        }else{
            return view('problem');
        }
    }

    public function resetPassword(){
        session_start();
        $validatedData = request()->validate([
            'password' => ['required', 'string','min:8' , 'confirmed']
        ]);
        $forgetPass = $_SESSION['forgetPass'];
        $user = User::where('id',$forgetPass->user_id)->first();
        if($user){
            $user->update([
                'password'  => bcrypt(request('password')),
            ]);

            UserForgotPassword::where('id',$forgetPass->id)->delete();
            session_destroy();
            return view('password-changed');
        }
        else{
            session_destroy();
            return view('problem');
        }
    }
}
