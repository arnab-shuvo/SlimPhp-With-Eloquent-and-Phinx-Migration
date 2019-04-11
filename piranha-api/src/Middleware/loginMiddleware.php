<?php

namespace App\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Auth;


class loginMiddleware
{
    public function __invoke(Request $request,Response $response, $next)
    {
        $token = $request->getHeader('Authorization');
        if($token == null){
            $error = "Authentication Failed";
            return $response->withJson($error)->withStatus(400);
        }
        $date = date('Y-m-d H:i:s');

        $res = Auth::select('session_key', 'expires_at' )->where('session_key' , $token)->where('expires_at', '>' , $date)->exists();

        if($res){
            $response = $next($request, $response);
            return $response;
        }
        else{
            $error = "Authentication Failed";
            return $response->withJson($error)->withStatus(400);
        }

    }
}