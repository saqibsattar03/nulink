<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserSalon;


class HairStylistController extends Controller
{
    public function hairStylists()
    {
    dd('sss');
        $hairStylist = UserSalon::all();
        dd($hairStylist);
        return response()->json(array([
            'success'=>'true',
            'msg'=>'hairstylist fetched',
            'hairstylists'=>$hairStylist
        ]));
    }
}
