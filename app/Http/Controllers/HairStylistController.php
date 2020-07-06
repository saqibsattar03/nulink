<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserSalon;


class HairStylistController extends Controller
{
    public function hairStylists()
    {
        $hairStylist = UserSalon::orderBy('created_at','desc')->get();
        // dd($hairStylist);
        return $hairStylist;
    }
}
