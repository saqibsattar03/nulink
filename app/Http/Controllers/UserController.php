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


        if($request->profile_pic){

            $file_path =  \App\User::where('id',$user_id)->first()['profile_pic'];

            if(file_exists($file_path)){
                @unlink($file_path);
            }

            $imagename = 'images/' . time(). '.' .$request->file('profile_pic')->getClientOriginalExtension();
            $request->file('profile_pic')->move(\public_path('images'),$imagename);

            \App\User::where('id',$user_id)->update(array(
                'profile_pic' =>$imagename,
               ));
        }



        // dd($user_id);
        \App\User::where('id',$user_id)->update(array(
         'name' =>$request->name,
         'phone' =>$request->phone,
        ));

        $user = User::where('id', $user_id)->first();
        $token = auth()->tokenById($user_id);
        $user->setAttribute('token', $token);
        $userSalon = \App\UserSalon::where('user_id', $user->id)->first();
        $user->setAttribute('userSalon', $userSalon);

        return $user;

    }

    public function editUserSalon(Request $request)
    {
        $user = $this->guard()->user();
        // $salon_id = $user->id;
        // dd($salon_id);

        if($request->image){
            $file_path = \App\UserSalon::where('user_id',$user->id)->first()['image'];
            $imagename = 'images/' . time(). '.' .$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(\public_path('images'),$imagename);


            \App\UserSalon::where('user_id',$user->id)->update(array(
                'image' =>$imagename,
               ));
            @unlink($file_path);
        }


       \App\UserSalon::where('user_id',$user->id)->update(array(
            'name'=>$request->name,
            'phone' =>$request->phone,
            'address' =>$request->address,
            'services' =>$request->services,
            'timing' =>$request->timing,
            'achievements' =>$request->achievements,
            'about' =>$request->about
        ));

        $userSalon = \App\UserSalon::where('user_id', $user->id)->first();
        return $userSalon;
//        $user->setAttribute('userSalon', $userSalon);
//
//        return $user;
        // dd($salon_id);
        }
}
