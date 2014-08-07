<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

use Server\Module as App;

class CustomMiddleware extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);
        $res->write('<h1>Hello, world!</h1>');
        return $res;
    }
}

$app = new App();

$app->employ([
    'class' => 'CustomMiddleware',
]);

$res = $app->call(new Server\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
));

$res->send(); // outputs: <h1>Hello, world!</h1>
