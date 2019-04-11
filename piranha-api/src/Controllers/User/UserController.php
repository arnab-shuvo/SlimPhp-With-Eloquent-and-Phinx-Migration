<?php

namespace App\Controllers\User;


use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Helpers\IsLoggedin;
use App\Helpers\IsAdmin;

class UserController
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

    public function index(Request $request, Response $response)
    {
        $user = User::all()->makeHidden(['created_at','updated_at','password'])->toArray();
        return $this->response->withJson($user)->withStatus(200);
    }

    public function store(Request $request, Response $response, array $args)
    {
        $allPostVars = $request->getParsedBody();
        $username = $allPostVars['username'];
        $email = $allPostVars['email'];
        $forename = $allPostVars['forename'];
        $surname = $allPostVars['surname'];
        $country = $allPostVars['country'];
        $gender = $allPostVars['gender'];
        $company = $allPostVars['company'];
        $password = $allPostVars['password'];
        $confirm_password = $allPostVars['confirm_password'];
        $role = 'user';

        $token = $this->request->getHeader('Authorization');

        if($allPostVars['role'] && !empty($token))
        {
            $isloggedin = IsLoggedin::isloggedin($token);
            $isadmin = IsAdmin::isadmin($token);
            if($isloggedin && $isadmin){
                $role = $allPostVars['role'];
            }
        }



        if($username == null || $email == null || $password == null || $confirm_password == null ){
            $error = "Validation Failed, Username, Email, Password and Confirm Password field should not be empty";
            return $this->response->withJson($error)->withStatus(400);
        }
        else{
            if (User::where('username', '=', $username)->exists()) {
                $error = "Username is already registered";
                return $this->response->withJson($error)->withStatus(400);
            }
            if (User::where('email', '=', $email)->exists()) {
                $error = "Email is already registered";
                return $this->response->withJson($error)->withStatus(400);
            }

            if (strlen($password) < 8) {
                $error = "Password length must be within 8-24 character";
                return $this->response->withJson($error)->withStatus(400);
            }
            if($password != $confirm_password){
                $error = "Password did not match, Confirm the password again";
                return $this->response->withJson($error)->withStatus(400);
            }
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $date = date('Y-m-d H:i:s');
        $create = User::insert([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'forename' => $forename,
                'surname' => $surname,
                'gender' => $gender,
                'country' => $country,
                'company' => $company,
                'created_at' =>$date,
                'role' => $role,
            ]);
        if($create){
            $user = User::where('email', $email)->where('username', $username)->first();
        }
        $user = $user->makeHidden(['created_at','updated_at','password']);
        return $this->response->withJson($user)->withStatus(200);
    }

    public function update(Request $request, Response $response, array $args)
    {
        $user = User::find($args['id']);
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
            if(User::where('username', '=', $username)->where('id' , '!=', $args['id'])->exists()){
                $error = "User name already exists";
                return $this->response->withJson($error)->withStatus(400);
            }
            else{
                $user->username = $allPostVars['username'];
            }
        }
        if ($allPostVars['email'])
        {
            if(User::where('email', '=', $email)->where('id' , '!=', $args['id'])->exists()){
                $error = "Email already exists";
                return $this->response->withJson($error)->withStatus(400);
            }
            else{
                $user->email = $allPostVars['email'];
            }
        }
        if ($allPostVars['forename'])
        {
            $user->forename = $allPostVars['forename'];
        }
        if ($allPostVars['surname'])
        {
            $user->surname = $allPostVars['surname'];
        }
        if ($allPostVars['country'])
        {
            $user->country = $allPostVars['country'];
        }
        if ($allPostVars['gender'])
        {
            $user->gender = $allPostVars['gender'];
        }
        if ($allPostVars['company'])
        {
            $user->company = $allPostVars['company'];
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

    public function delete(Request $request, Response $response, array $args)
    {

        $user = User::find($args['id']);
        if ( is_null($user) ) {
            $error = "User Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }
        $delete = $user->delete();
        $message = "User deleted successfully";
        return $this->response->withJson($message)->withStatus(200);
    }

    public function user_detail(Request $request, Response $response, array $args)
    {

        $user = User::find($args['id'])->first()->makeHidden(['password']);
        if ( is_null($user) ) {
            $error = "User Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }
        return $this->response->withJson($user)->withStatus(200);
    }

}