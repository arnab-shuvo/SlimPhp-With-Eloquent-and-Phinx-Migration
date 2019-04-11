<?php

namespace App\Controllers\Auth;

use Illuminate\Support\Str;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Auth;
use App\Models\User;
use App\Helpers\SessionKeyGenerator;
use App\Helpers\GetClientIp;


class AuthController
{
    protected $container;

    public function __construct($container){
        $this->container = $container;
    }
    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function login(Request $request, Response $response)
    {
        $allPostVars = $request->getParsedBody();
        $password = $allPostVars['password'];
        $username = $allPostVars['username'];
        if($username == null || $password == null){
            $error = "Validation Failed, User name and Password required";
            return $this->response->withJson($error)->withStatus(400);
        }

//        echo $username;
        $user = User::where('username', $username)->first();
        if (is_null($user)){
            $error = "Validation Failed, User name or Password didnot match";
            return $this->response->withJson($error)->withStatus(400);
        }
        $query_password = $user->password;
        $user_id = $user->id;
        $ip = GetClientIp::client_ip();
        $date = date('Y-m-d H:i:s');

        if (!(password_verify($password, $query_password))) {
            $error = "Validation Failed, User name or Password didnot match";
            return $this->response->withJson($error)->withStatus(400);
        }

        $res = Auth::select('user_ip', 'expires_at' )->where('user_id', $user_id)->first();


        if ($res && $res->expires_at > $date && $res->user_ip == $ip){
            $error = "User is already logged in";
            return $this->response->withJson($error)->withStatus(200);
        }

        $session_key =  SessionKeyGenerator::gen(32);

        $stop_date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));

        $expires_at = $stop_date;

        $create = Auth::insert([
            'user_id' => $user_id,
            'session_key' => $session_key,
            'expires_at' => $expires_at,
            'user_ip' => $ip,
        ]);

        $res = Auth::select('session_key','expires_at', 'user_id')->with(array('user'=>function($query){
            $query->select('id','forename', 'surname', 'email', 'role');
        }))->where('session_key', $session_key)->first();

        return $this->response->withJson($res)->withStatus(200);
    }

    public function logout(Request $request, Response $response)
    {
        $token = $this->request->getHeader('Authorization');
        $auth = Auth::where('session_key', $token)->first();
        $delete = $auth->delete();
        if ($delete){
            $res = 'Logged out';
            return $this->response->withJson($res)->withStatus(200);
        }
        else{
            $res = 'Something went wrong, try again later';
            return $this->response->withJson($res)->withStatus(400);
        }

    }



    public function renew_password(Request $request, Response $response)
    {
        $token = $this->request->getHeader('Authorization');
        $now = date('Y-m-d H:i:s');
        $session_key = Auth::select('expires_at', 'user_id')->where('session_key', $token)->first();
        if (empty($session_key)) {
            $error = "Authentication Failed";
            return $this->response->withJson($error)->withStatus(400);
        }
        elseif ($now > $session_key->expires_at){
            $error = "Authentication Failed, Login Required";
            return $this->response->withJson($error)->withStatus(400);
        }


        $user_id = $session_key->user_id;
        $session_key =  SessionKeyGenerator::gen(32);
        $new_date = date('Y-m-d H:i:s', strtotime($now . ' +1 day'));
        $ip = GetClientIp::client_ip();

        $update = Auth::where('user_id', $user_id)->where('user_ip', $ip)->where('session_key', $token)
                    ->update(['session_key' => $session_key, 'expires_at' => $new_date ]);

        if ($update == 1){
            $new_session_key = Auth::select('session_key', 'expires_at')->where('user_id', $user_id)->where('user_ip', $ip)->where('session_key', $session_key)->first();
            return $this->response->withJson($new_session_key)->withStatus(200);
        }
        else{
            $error = "Something Went Wrong, Please try again later";
            return $this->response->withJson($error)->withStatus(400);
        }


    }

}