<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profile';

    protected $fillable = ['uid','nickName', 'country', 'province', 'city', 'language','avatarUrl','gender'];

}
