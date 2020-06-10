<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEdit extends Model
{
    protected $fillable = [
        'user_id', 'name', 'image'
    ];
}
