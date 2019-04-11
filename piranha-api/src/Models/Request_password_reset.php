<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Request_password_reset extends Model
{
    public $timestamps = false;
    protected $table = 'request_password_reset';
    protected $fillable = ['hash_key ', 'already_used', 'user_id','expires_at'];
}

