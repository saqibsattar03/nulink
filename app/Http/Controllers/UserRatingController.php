<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserRatingController extends Controller
{
    public function userRating($id)
    {
        $total = 0;
        $count = 0;
        $ratings = UserRating::where('recipient_id',$id)->get();


        // dd($hairStylist);
        // echo $hairStylist;
        foreach($ratings as $rating)
        {
            // echo $rating->rating;
            $total = $total + $rating->rating;
            $count++;

        }

        if ($count > 0) {
            $avgRating = $total / $count;
            return response()->json([
                'success' => 'true',
                'rating' => $avgRating,
                'count' => $count
            ]);
        }

        return response()->json([
            'success' => 'true',
            'rating' => 0,
            'count' => 0
        ]);

        // $hairStylist->id;
    }

    public function postUserRating(Request $request)
    {
        $user = $this->guard()->user();



        $rated = UserRating::where('sender_id',$user->id)->where('recipient_id',$request->recipient_id)->first();
        if($rated){
            return \response()->json([
                'error' => 'You have already rated this hairstylist!'
            ],500);
        }

        UserRating::create([
                'sender_id' => $user->id,
                'recipient_id' => $request->recipient_id,
                'rating' => $request->rating
            ]);



            $sum =0.0;

            $ratings = UserRating::where('recipient_id',$request->recipient_id)->get();
            foreach($ratings as $rating)
            {
                $sum +=$rating->rating;
            }

            $count = count($ratings);

            if($count>0)
            {
                $sum /= $count;
            }

            \App\UserSalon::where('id',$request->recipient_id)->update(array(
                "rating" =>$sum
            ));

        // $sum = 0.0;
        // $ratings = UserRating::where('recipient_id', $request->recipient_id)->get();

        // foreach ($ratings as $rating) {
        //     $sum += $rating->rating;
        // }

        // $count = count($ratings);

        // if ($count > 0) {
        //     $sum /= $count;

        //     \App\User::where('id', $request->recipient_id)->update(array(
        //         "rating" => $sum
        //     ));
        // }

        return \response()->json([
            'success' => 'true',
            'message' => 'User rating Posted Successfully'
        ]);
    }

     /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
