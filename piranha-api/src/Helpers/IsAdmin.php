<?php
/**
 * Created by PhpStorm.
 * User: arnab
 * Date: 4/1/19
 * Time: 12:54 PM
 */

namespace App\Helpers;
use App\Models\User;
use App\Models\Auth;


class IsAdmin
{
    public function isadmin($token)
    {
        $date = date('Y-m-d H:i:s');
        $user_id =   Auth::select('user_id')->where('session_key', $token)->where('expires_at', '>' , $date)->first();

        $isadmin = User::where('id', $user_id->user_id)->where('role',  'admin')->exists();
        if($isadmin == 1){
            return true;
        }
        else{
            return false;
        }
    }
}