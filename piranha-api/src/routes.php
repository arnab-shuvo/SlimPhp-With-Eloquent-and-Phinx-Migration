<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\User\UserController;
use App\Controllers\Auth\AuthController;
use App\Controllers\Profile\ProfileController;
use App\Controllers\Auth\PasswordController;
use App\Controllers\Products\ProductController;
use App\Middleware\loginMiddleware as isLoggedin;
use App\Middleware\roleCheck as RoleCheck;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


//$app->get('/user', UserController::class . ':index') ;
//


//// Api Routes
$app->group('/api',
    function () {

        // Auth Routes
        $this->post('/auth', AuthController::class . ':login'); //login
        $this->put('/auth/renew', AuthController::class . ':renew_password')->add( new isLoggedin() ); //Renew Session Key
        $this->post('/auth/forget_password', PasswordController::class . ':forget_password'); //Request for activation Link
        $this->post('/auth/reset_password', PasswordController::class . ':reset_password'); //Reset Password
        $this->delete('/auth/logout', AuthController::class . ':logout')->add( new isLoggedin() );; //Logout


        // User Routes
        $this->get('/users', UserController::class . ':index')->add( new isLoggedin() )->add( new RoleCheck() ); //Get User by admin
        $this->get('/users/{id}', UserController::class . ':user_detail')->add( new isLoggedin() )->add( new RoleCheck() ); //Get User by admin
        $this->post('/users', UserController::class . ':store'); //add user by admin also register as user....Role can be assigned if its by admin
        $this->delete('/users/{id}', UserController::class . ':delete')->add( new isLoggedin() )->add( new RoleCheck() ); //delete a user by admin
        $this->put('/users/{id}', UserController::class . ':update')->add( new isLoggedin() )->add( new RoleCheck() );//update an user by admin

        //Personal Profile Update
        $this->get('/my_profile', ProfileController::class . ':my_profile')->add( new isLoggedin() ); //Update my profile
        $this->put('/my_profile', ProfileController::class . ':update')->add( new isLoggedin() ); //Update my profile
        $this->put('/change_password', PasswordController::class . ':changepass')->add( new isLoggedin() ); //change my password

        //Product operation
        $this->get('/products', ProductController::class . ':index'); //get all products
        $this->post('/products', ProductController::class . ':store')->add( new isLoggedin() )->add( new RoleCheck() ); //add products by admin
        $this->get('/products/{id}', ProductController::class . ':detail'); //get products detail
        $this->put('/products/{id}', ProductController::class . ':update')->add( new isLoggedin() )->add( new RoleCheck() ); //update product by admin
        $this->delete('/products/{id}', ProductController::class . ':delete')->add( new isLoggedin() )->add( new RoleCheck() );//Delete product by admin

    });

