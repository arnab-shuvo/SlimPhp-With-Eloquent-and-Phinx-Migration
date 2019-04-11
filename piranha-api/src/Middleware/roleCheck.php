<?php

namespace App\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Auth;
use App\Models\User;


class roleCheck
{
    public function __invoke(Request $request,Response $response, $next)
    {
        $token = $request->getHeader('Authorization');
        if($token == null){
            $error = "Authentication Failed";
            return $response->withJson($error)->withStatus(400);
        }
        $date = date('Y-m-d H:i:s');
        $isloggedin =   Auth::select('user_id')->where('session_key', $token)->first();

        $user_id = $isloggedin->user_id;

        $isadmin =   User::where('id', $user_id)->where('role', 'admin')->exists();

        if ($isadmin){
            $response = $next($request, $response);
            return $response;

        }
        else{
            $error = "You dont have permission to execute this operation";
            return $response->withJson($error)->withStatus(400);
        }
    }
}