<?php

namespace App\Controllers\Products;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Auth;
use App\Models\User;
use App\Models\Products;
use App\Helpers\GetUserId;

class ProductController
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
    public function index()
    {

        $products = Products::with(array('created_by'=>function($query){
            $query->select('id','forename', 'surname', 'email', 'role');
        }))->orderBy('id', 'DESC')->get();
        return $this->response->withJson($products)->withStatus(200);
    }
    public function store(Request $request, Response $response)
    {
        $token = $this->request->getHeader('Authorization');
        $user_id = GetUserId::getUserId($token);
        $allPostVars = $request->getParsedBody();
        $name = $allPostVars['name'];

        if($name == null){
            $error = "Product name is required";
            return $this->response->withJson($error)->withStatus(400);
        }
        $create = new Products();
        $create->name = $name;
        $create->created_by = $user_id;
        $create->save();
        return $this->response->withJson($create)->withStatus(200);
    }
    public function detail(Request $request, Response $response, array $args)
    {
        $product = Products::find($args['id']);
        if ( is_null($product) ) {
            $error = "Product Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }
        return $this->response->withJson($product)->withStatus(200);

    }
    public function update(Request $request, Response $response, array $args)
    {
        $product = Products::find($args['id']);
        if ( is_null($product) ) {
            $error = "Product Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }
        $allPostVars = $request->getParsedBody();
        $name = $allPostVars['name'];
        $product->name = $name;
        $product->save();
        return $this->response->withJson($product)->withStatus(200);

    }
    public function delete(Request $request, Response $response, array $args)
    {
        $product = Products::find($args['id']);
        if ( is_null($product) ) {
            $error = "Product Doesn't exist";
            return $this->response->withJson($error)->withStatus(400);
        }
        $delete = $product->delete();
        if ($delete){
            $msg = "Product has been deleted successfully";
            return $this->response->withJson($msg)->withStatus(200);
        }
        else{
            $error = "Something went wrong";
            return $this->response->withJson($error)->withStatus(400);
        }

    }


}