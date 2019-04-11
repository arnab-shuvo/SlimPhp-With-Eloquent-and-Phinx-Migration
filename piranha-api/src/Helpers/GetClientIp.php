<?php
/**
 * Created by PhpStorm.
 * User: arnab
 * Date: 3/27/19
 * Time: 6:04 PM
 */

namespace App\Helpers;


class GetClientIp
{
    public function client_ip(){
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        else{
            $ip = "unknown";
        }
        return $ip;
    }
}