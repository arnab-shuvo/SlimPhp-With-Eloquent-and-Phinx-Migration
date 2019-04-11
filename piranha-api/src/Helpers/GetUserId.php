<?php
/**
 * Created by PhpStorm.
 * User: arnab
 * Date: 4/1/19
 * Time: 3:59 PM
 */

namespace App\Helpers;
use App\Models\Auth;

class GetUserId
{
    public function getUserId($token)
    {
        $date = date('Y-m-d H:i:s');
        $user_id =   Auth::select('user_id')->where('session_key', $token)->where('expires_at', '>' , $date)->first();
        return $user_id->user_id;
    }
}