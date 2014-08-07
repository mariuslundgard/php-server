<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

use Server\Module;
// use Server\Layer;
// use Server\Request;
// use Server\Error;
use Server\Controller;

class TestController extends Controller
{
    public function index()
    {
        d('index');
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

$module = new Module();

$module->map([
    'pattern' => '*',
    'controller' => 'TestController',
]);

$res = $module->call();

$res->send();
