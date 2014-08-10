<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

// class CustomMiddleware extends Server\Layer
// {
//     public function call(Server\Request $req = null, Server\Error $err = null)
//     {
//         $res = parent::call($req, $err);
//         $res->write('<h1>Hello, world!</h1>');
//         return $res;
//     }
// }

// $app = new Server\Module(null, array(), $_SERVER);

// $app->employ([
//     'class' => 'CustomMiddleware',
// ]);

// $res = $app->call(new Server\Request(
//     $_SERVER['REQUEST_METHOD'],
//     $_SERVER['REQUEST_URI']
// ));

//$res->send(); // outputs: <h1>Hello, world!</h1>


// d($app);

// $res = $app->call();

// d()

$layer = new Server\Layer(new Server\Layer());

d($layer->dump());

$res = $layer->call(new Server\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    array( 'test' => '1' ),
    array( 'Accept' => 'text/html' )
));

d($res);
