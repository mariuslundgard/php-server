<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class TestController extends Controller
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

class ModuleTest extends Base
{
    public function testCreate()
    {
        $module = new Module();

        $this->assertInstanceOf('Server\Module', $module);
    }

    public function testHandleRequest()
    {
        $module = new Module();

        $res = $module->call();

        $this->assertInstanceOf('Server\Response', $res);
    }

    public function testMap()
    {
    	$module = new Module();

    	$module->map([
    		'pattern' => '*',
    		'fn' => function ($req, $res) {

    		}
    	]);
    }

    public function testMapToController()
    {
        $module = new Module();

        $module->map([
            'pattern' => '*',
            'controller' => 'Server\TestController',
        ]);

        $res = $module->call();

        $this->assertEquals('test', $res->body);
    }

    public function testMapToControllerReturnString()
    {
        $module = new Module();

        $module->map([
            'pattern' => '*',
            'controller' => 'Server\TestController',
            'action' => 'writeByReturn'
        ]);

        $res = $module->call();

        $this->assertEquals('test', $res->body);
    }

    public function testMapToControllerReturnResponseData()
    {
        $module = new Module();

        $module->map([
            'pattern' => '*',
            'controller' => 'Server\TestController',
            'action' => 'returnData'
        ]);

        $res = $module->call();

        $this->assertEquals('test', $res->data['test']);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testMapToNonexistentControler()
    {
        $module = new Module();

        $module->map([
            'controller' => 'Server\NonexistingController',
        ]);

        $res = $module->call();
    }

    public function testMapToCallback()
    {
        $module = new Module();

        $module->map(array(
            'fn' => function ($req, $res) {
                $res->write('test');
            }
        ));

        $res = $module->call();

        $this->assertEquals('test', $res->body);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testMapToInsufficientRoute()
    {
        $module = new Module();

        $module->map([]);

        $res = $module->call();
    }

}
