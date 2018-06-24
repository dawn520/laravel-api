<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $table = 'sms';

    protected $fillable = ['uid','phone', 'code', 'used', 'type', 'ip'];

}
