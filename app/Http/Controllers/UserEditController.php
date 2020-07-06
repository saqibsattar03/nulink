<?php

namespace App\Http\Controllers;

use App\UserEdit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class UserEditController extends Controller
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


    public function userEdit(Request $request)
    {
        $user = $this->guard()->user();
        $userEdits = UserEdit::where('user_id',$user->id)->get();
        return $userEdits;
    }

    public function userUpdate(Request $request)
    {

        // dd('sdhsvds');
        // $this->validate($request,[
        //     'image' => 'required|image:png,jpg,jpeg'
        // ]);
        $user = $this->guard()->user();
        // dd($user);
        $rule = ['image' => 'required|image:png,jpg,jpeg'];

        $input = $request->only('image');

        $validator = Validator::make($input,$rule);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->messages()
            ],500);
        }

        if($request->file('image')){

        $imagename = 'images/' . time(). '.' .$request->file('image')->getClientOriginalExtension();
        // dd($imagename);
        $request->file('image')->move(\public_path('images'),$imagename);

        UserEdit::create([
            'user_id' => $user->id,
            'name' => $request->name == null ? "Untitled" : $request->name,
            'image' => $imagename
            ]);
        return response()->json([ 'message' => 'Successfully added User Edit.']);
        }

    }

    public function deleteImage($id){
        $image_path = UserEdit::select('image')->where('id',$id)->first();
        $file_path = $image_path->image;

        if(file_exists($file_path)){
            @unlink($file_path);
            UserEdit::where('id',$id)->delete();
        }
        else
        {
            UserEdit::where('id',$id)->delete();
        }

        return response()->json(['success' => 'true', 'message' => 'Image Deleted Successfully' ]);

    }
}
