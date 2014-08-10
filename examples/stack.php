<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class BodyWriterMiddleware extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->write($this->config['body'] ? $this->config['body'] : 'test-layer');

        return $res;
    }
}

$stack = new Server\Module();

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

$req = new Server\Request('GET', '/foo');

$res = $stack->call($req);

$res->send();
