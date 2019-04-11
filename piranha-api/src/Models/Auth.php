<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model{
    protected $table = 'auth';
    protected $fillable = ['session_key ','user_id','expires_at'];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}

