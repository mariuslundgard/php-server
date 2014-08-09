<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class TestController extends Server\Controller
{
    public function index()
    {
        $this->res->write('test');
    }

    public function writeByReturn()
    {
        return 'test';
    }

    public function returnData()
    {
        return array( 'test' => 'test' );
    }
}

$module = new Server\Module();

$module->map([
    'pattern' => '*',
    'controller' => 'TestController',
]);

$res = $module->call();

$res->send();
