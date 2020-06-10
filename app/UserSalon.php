<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserSalon extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id','name', 'phone', 'address','services','timing','achievements'
    ];
}
