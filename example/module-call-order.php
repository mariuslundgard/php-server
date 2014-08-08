<?php

//////////////////////////////////////////////////////////////////////////////
putenv('DEBUG=1');
require __DIR__.'/../vendor/autoload.php';

//////////////////////////////////////////////////////////////////////////////
class Renderer extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);
        $res->body = '<pre style="border: 3px solid #00f; padding: 1em;">rendered: '.$res->body.'</pre>';

        return $res;
    }
}

//////////////////////////////////////////////////////////////////////////////
class Compression extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);
        // $res->write('gzip:');
        $res->body = '<pre style="border: 3px solid #f00; padding: 1em;">compressed: '.$res->body.'</pre>';

        return $res;
    }
}

//////////////////////////////////////////////////////////////////////////////
$app = new Server\Module();

$app->employ(array( 'class' => 'Compression' ));
$app->employ(array( 'class' => 'Renderer' ));

$app->map(array( 'fn' => function ($req, $res) { $res->write('CONTENT'); }));

$res = $app->call();

$res->send();
