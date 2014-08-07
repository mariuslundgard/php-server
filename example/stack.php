<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

use Server\Module;
use Server\Layer;
use Server\Request;
use Server\Error;

class BodyWriterMiddleware extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->write($this->config['body'] ? $this->config['body'] : 'test-layer');

        return $res;
    }
}

$stack = new Module();

$stack->employ(array(
    'pattern' => '/foo*',
    'class' => 'BodyWriterMiddleware',
    'config' => array( 'body' => 'foo' )
));

$stack->employ(array(
    'pattern' => '/bar*',
    'class' => 'BodyWriterMiddleware',
    'config' => array( 'body' => 'bar' )
));

$req = new Request('GET', '/foo');

$res = $stack->call($req);

$res->send();
