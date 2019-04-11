<?php
/**
 * Created by PhpStorm.
 * User: arnab
 * Date: 4/1/19
 * Time: 12:41 PM
 */

namespace App\Helpers;
use App\Models\Auth;


class IsLoggedin
{
    public function isloggedin($token)
    {
        $date = date('Y-m-d H:i:s');
        $isloggedin =   Auth::where('session_key', $token)->where('expires_at', '>' , $date)->exists();
        return $isloggedin;
    }

}