<?php

namespace App\Controllers\Auth;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Models\Auth;
use App\Models\Request_password_reset;
use App\Helpers\SessionKeyGenerator;
use App\Helpers\Mailer;
use App\Helpers\ForgetPasswordMailTemplate;


class PasswordController
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
    public function forget_password(Request $request, Response $response)
    {
        $allPostVars = $request->getParsedBody();
        $email = $allPostVars['email'];

        $user = User::where('email', $email)->first();

        if ($user == null){
            $error = "Email Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }

        $now = date('Y-m-d H:i:s');
        $hash_key =  SessionKeyGenerator::gen(32);
        $user_id = $user->id;
        $expires_at =  $new_date = date('Y-m-d H:i:s', strtotime($now . ' +1 day'));
        $create = Request_password_reset::insert([
            'user_id' => $user_id,
            'hash_key' => $hash_key,
            'expires_at' => $expires_at
        ]);
        if ($create)
        {
            $link =  $_SERVER['SERVER_NAME'].'/resetpassword?token='.$hash_key;
            $subject = 'Password Reset Confirmation';
            $body = ForgetPasswordMailTemplate::forgetPasswordMailTemplate($link);
            $mailto = $user->email;
            $mail = Mailer::send_mail($body, $subject, $mailto);
            if ($mail)
            {
                $msg = 'Mail sent';
                return $this->response->withJson($msg)->withStatus(200);
            }
            else{
                $msg = 'Mail not sent. Something went wrong';
                return $this->response->withJson($msg)->withStatus(400);
            }

        }

    }

    public function reset_password(Request $request, Response $response)
    {
        $token = $this->request->getHeader('ResetToken');
        $allPostVars = $request->getParsedBody();
        $newpassword = $allPostVars['new_password'];
        $confirmpassword = $allPostVars['confirm_password'];

        $now = date('Y-m-d H:i:s');

        $auth = Request_password_reset::where('hash_key', $token)->where('expires_at', '>', $now)->where('already_used', 0)->first();
        if ($auth==null)
        {
            $error = 'Session Expired, Try Again';
            return $this->response->withJson($error)->withStatus(400);
        }
        $user_id = $auth->user_id;
        $user = User::where('id', $user_id)->first();
//
//        if ( !$pass_varify){
//            $error = 'Wrong password, Please Try Again';
//            return $this->response->withJson($error)->withStatus(400);
//        }
        if ($newpassword != $confirmpassword){
            $error = 'Password mismatched, Please Try Again';
            return $this->response->withJson($error)->withStatus(400);
        }
        elseif (strlen($newpassword) < 8 || strlen($confirmpassword) < 8){
            $error = 'Password must be within 8-24 characters';
            return $this->response->withJson($error)->withStatus(400);
        }

        $password = password_hash($newpassword, PASSWORD_DEFAULT);
        $user->password = $password;
        $update = $user->save();
        $auth = Request_password_reset::where('hash_key', $token)->where('expires_at', '>', $now)->where('already_used', 0)->first();
        $auth->already_used = true;
        $auth->expires_at = $now;

        $reset_pass_null = $auth->save();


        if($update)
        {
            $msg = 'Password has been updated successfully';
            return $this->response->withJson($msg)->withStatus(200);

        }
        else{
            $error = 'Something went wrong';
            return $this->response->withJson($error)->withStatus(400);
        }

    }
    public function changepass(Request $request, Response $response)
    {
        $token = $this->request->getHeader('Authorization');
        $now = date('Y-m-d H:i:s');
        $allPostVars = $request->getParsedBody();
        $oldpassword = $allPostVars['old_password'];
        $newpassword = $allPostVars['new_password'];
        $confirmpassword = $allPostVars['confirm_password'];
        $auth = Auth::where('session_key', $token)->where('expires_at', '>', $now)->first();
        $user_id = $auth->user_id;
        $user = User::where('id', $user_id)->first();
        $pass_varify = password_verify($oldpassword, $user->password);
        if (!$pass_varify){
            $error = 'Authorization failed.';
            return $this->response->withJson($error)->withStatus(400);
        }
        if ($newpassword != $confirmpassword){
            $error = 'Password mismatched';
            return $this->response->withJson($error)->withStatus(400);
        }
        if (strlen($newpassword) < 8){
            $error = 'Password must be within 8-24 characters';
            return $this->response->withJson($error)->withStatus(400);
        }
        $password = password_hash($newpassword, PASSWORD_DEFAULT);
        $user->password = $password;
        $update = $user->save();
        if($update){
            $msg = "Password has been updated successfully";
            return $this->response->withJson($msg)->withStatus(200);
        }

    }

}