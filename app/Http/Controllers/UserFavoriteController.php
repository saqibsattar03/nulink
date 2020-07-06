<?php

namespace App\Http\Controllers;

use App\UserFavorite;
use App\UserSalon;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

class UserFavoriteController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

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

    /**
     * Get the authenticated User Favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFavorites()
    {
        // dd('bhsh');
        $user = $this->guard()->user();
        // $userFavorites = UserFavorite::where('user_id', $user->id)->get();
        // $userFavoriteList = [];
        // foreach($userFavorites as $userFavorite){
        //     $userFav = User::where('id',$userFavorite->id)->first();
        //     array_push($userFavoriteList,$userFav);
        // }
        // return $userFavoriteList;

         $userFavoriteList = [];
            $userFavoriteList = UserFavorite::where('user_id',$user->id)->pluck('hairstylist_id')->toArray();
            
            $saved = [];
            for ($i  = 0; $i < count($userFavoriteList); $i++) {
                array_push($saved, UserSalon::where('id', $userFavoriteList[$i])->get()[0]);
            }
            
        return $saved;
     
    }

    // public function getHairStylist()
    // {
    //     $hairstylists = [];
    //     $ids = UserFavorite::all('hairstylist_id');

    //     foreach ($ids as $id) {
    //         array_push($hairstylists, \App\User::where('id', $id['hairstylist_id'])->get()[0]);    
    //     }

    //     return response()->json($hairstylists);
    // }

    public function postFavorite(Request $request)
    {
        $rules = [
            'hairStylistId' => 'required'
        ];

        $input = $request->only(
            'hairStylistId'
        );
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        } else {
            $user = $this->guard()->user();
            $favorites = UserFavorite::where('user_id', $user->id)->get();
            foreach ($favorites as $favorite) {
                if ($favorite->hairstylist_id == $request->hairStylistId) {
                    UserFavorite::where('id', $favorite->id)->delete();
                    return response()->json(['success' => true, 'message' => 'Successfully removed hairstylist from favorite list.']);
                } else {
                    continue;
                }
            }
            UserFavorite::create([
                'user_id' => $user->id,
                'hairstylist_id' => $request->hairStylistId
            ]);
            return response()->json(['success' => true, 'message' => 'Successfully added hairstylist to favorite list.']);
        }
    }
}
