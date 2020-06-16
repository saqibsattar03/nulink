<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailVerification;
use App\User;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function sendEmail(Request $request)
    {
        $user = User::where('email',$request->email)->first();

        if($user)
        {
            $token = bcrypt($user->email);
            $emailverification = new EmailVerification();
            $emailverification->user_id = $user->id;
            $emailverification->token = $token;

            $emailverification->save();
            // http://locslhodt:234/verfiy/123

            $data = array(
                'id' => $emailverification->id,
                'name' =>$user,
                'token' => $token

            );
            Mail::to($user->email)->send(
                new EmailVerificationMail($data, 'Email Verification')
            );
            return 'true';
        }
         else
        {
            return 'false';
        }
    }

    
    function verify() {
        $verifyEmail = EmailVerification::where('id',request('id'))->first();
        // $user = User::where('id',$verifyEmail->user_id);
        if($verifyEmail)
        {
            if($verifyEmail->token == request('token'))
            {
                User::where('id',$verifyEmail->user_id)->update(['status'=>true]);
                $verifyEmail->delete();
                return view('success');
            }
        }
        else
            {
                return view('error');
            }
        // $user = User::where('id', $id)->get()[0];
        // $user->status = "verified";
        // $user->save();
    }
}




