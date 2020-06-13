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
        // dd('hvjvvh');
        $user = $this->guard()->user();

        // dd($user);   `
        $userid = $user->id;
        // dd($userid);

        $userEdits = UserEdit::where('user_id',$userid)->get();
        // dd($userEdits);
        return $userEdits;
        // return response()->json(array([
        //     'userEdits' => $userEdits,
        //     'success' => 'true',
        //     'message' => 'User Edits Successfully retrieved'
        // ]));
        
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
                'success' => 'false',
                'error' => $validator->messages()
            ]);
        }

        if($request->file('image')){

        $imagename = time(). '.' .$request->file('image')->getClientOriginalExtension();
        // dd($imagename);
        $request->file('image')->move(\public_path('images/useredits'),$imagename);
        
        UserEdit::create([
            'user_id' => $user->id,
            'name' => $request->name == null ? "Untitled" : $request->name,
            'image' => $imagename 
            ]);
        return response()->json(['success' => 'true', 'message' => 'Successfully added User Edit.']);
        }

    }

    public function deleteImage($id){
        $image_path = UserEdit::select('image')->where('id',$id)->first();
        // dd($image_path);
        $file_path = 'images/useredits/' .$image_path->image;
        // dd($file_path);

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
