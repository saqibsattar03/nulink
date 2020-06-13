<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{
     /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }


    public function getUser()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function editUserProfile(Request $request)
    {
        $user = $this->guard()->user();
        $user_id = $user->id;
        // dd($user_id);
        \App\User::where('id',$user_id)->update(array(
         'name' =>$request->name,
         'phone' =>$request->phone,
        //  'password' =>$request->password,
         'profile_pic' =>$request->profile_pic,
         'about' =>$request->about
        ));
        return 'lund kha';

    }

    public function editUserSalon(Request $request)
    {
        $user = $this->guard()->user();
        // $salon_id = $user->id;
        // dd($salon_id);
        \App\UserSalon::where('user_id',$user->id)->update(array(
            'name'=>$request->name,
            'phone' =>$request->phone,
            'address' =>$request->address,
            'services' =>$request->services,
            'timing' =>$request->timing
            
        ));
        return response()->json(
            ['success'=> true,
    'msg'=>'fuck off 🖕']
);
        // dd($salon_id);
        }
}
