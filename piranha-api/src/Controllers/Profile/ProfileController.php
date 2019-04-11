<?php
namespace App\Controllers\Profile;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Models\Auth;
use App\Helpers\GetUserId;

class ProfileController
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

    public function update(Request $request, Response $response, array $args)
    {
        $token = $this->request->getHeader('Authorization');
        $token_query = Auth::select('expires_at', 'user_id')->where('session_key', $token)->first();
        $date = date('Y-m-d H:i:s');
        if ( is_null($token) && ($date < $token_query->expires_at)) {
            $error = "Authorization Failed";
            return $this->response->withJson($error)->withStatus(400);
        }

        $user = User::find($token_query->user_id);
        if ( is_null($user) ) {
            $error = "User Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }
        $allPostVars = $request->getParsedBody();
        $username = $allPostVars['username'];
        $email = $allPostVars['email'];

        $old_user = $user;

        if ($allPostVars['username'])
        {
            if(User::where('username', '=', $username)->where('id' , '!=', $token_query->user_id)->exists()){
                $error = "User name already exists";
                return $this->response->withJson($error)->withStatus(400);
            }
            else{
                $user->username = $allPostVars['username'];
            }
        }
        if ($allPostVars['email'])
        {
            if(User::where('email', '=', $email)->where('id' , '!=', $token_query->user_id)->exists()){
                $error = "Email already exists";
                return $this->response->withJson($error)->withStatus(400);
            }
            else{
                $user->email = $allPostVars['email'];
            }
        }
        if ($allPostVars['forename'])
        {
            $user->email = $allPostVars['forename'];
        }
        if ($allPostVars['surname'])
        {
            $user->email = $allPostVars['surname'];
        }
        if ($allPostVars['country'])
        {
            $user->email = $allPostVars['country'];
        }
        if ($allPostVars['gender'])
        {
            $user->email = $allPostVars['gender'];
        }
        if ($allPostVars['company'])
        {
            $user->email = $allPostVars['company'];
        }


        $update = $user->save();
        if($update)
        {
            $msg = 'User Information has been updated successfully';
            return $this->response->withJson($msg)->withStatus(200);

        }
        else{
            $error = 'Something went wrong';
            return $this->response->withJson($error)->withStatus(400);
        }


    }

    public function my_profile(Request $request, Response $response, array $args)
    {
        $token = $this->request->getHeader('Authorization');
        $user_id = GetUserId::getUserId($token);
        $user = User::find($user_id)->first()->makeHidden(['password']);
        return $this->response->withJson($user)->withStatus(200);
    }


}