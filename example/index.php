<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class CustomMiddleware extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);
        $res->write('<h1>Hello, world!</h1>');
        return $res;
    }
}

$app = new Server\Module();

$app->employ([
    'class' => 'CustomMiddleware',
]);

$res = $app->call(new Server\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
));

$res->send(); // outputs: <h1>Hello, world!</h1>
