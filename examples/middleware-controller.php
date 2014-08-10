<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class HeaderFilter extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->body = '<h1>'.$res->body.'</h1>';

        return $res;
    }
}

class FrontController extends Server\Controller
{
    public function index()
    {
        return 'Hello, world!';
    }
}

$app = new Server\Module();

$app->employ([
    'class' => 'HeaderFilter',
]);

$app->map([
    'controller' => 'FrontController',
]);

$app->call()->send(); // outputs: <h1>Hello, world!</h1>
