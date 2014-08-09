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

class TestModule extends Module
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->write($this->config['body'] ? $this->config['body'] : 'test-layer');

        return $res;
    }
}

class UriWriterModule extends Module
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->write($this->config->get('uri', $req->uri));

        return $res;
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

    public function testChainedCall()
    {
        $res = (new Module())

            ->employ(array(
                'class' => 'Server\TestModule'
            ))
            ->employ(array(
                'class' => 'Server\TestModule'
            ))
            ->employ(array(
                'class' => 'Server\TestModule'
            ))

            ->map([
                'controller' => 'Server\TestController',
            ])

            ->call();

        $this->assertEquals('testtest-layertest-layertest-layer', $res->body);
    }

    public function testCallOrder()
    {
        $app = new Module();

        $app->employ(array( 'class' => 'Server\TestModule', 'config' => array( 'body' => '1:' )));
        $app->employ(array( 'class' => 'Server\TestModule', 'config' => array( 'body' => '2:' )));
        $app->employ(array( 'class' => 'Server\TestModule', 'config' => array( 'body' => '3:' )));
        $app->employ(array( 'class' => 'Server\TestModule', 'config' => array( 'body' => '4:' )));

        $app->map(array( 'fn' => function ($req, $res) { $res->write('test:'); }));

        $this->assertEquals('test:4:3:2:1:', $app->call()->body);
    }

    public function testNestedApp()
    {
        $subModule1 = new UriWriterModule();
        $subModule2 = new UriWriterModule();

        $app = (new Module())
            ->employ(array( 'pattern' => '/test1*uri', 'instance' => $subModule1 ));

        $subModule1
            ->employ(array( 'pattern' => '/test2*uri', 'instance' => $subModule2 ));

        $subModule2
            ->employ(array( 'pattern' => '/te*uri', 'class' => 'Server\UriWriterModule' ));

        $res = $app->call(new Request('GET', '/test1/test2/test'));

        $this->assertEquals('st/test/test2/test', $res->body);
    }

    public function testCallDoneModule()
    {
        $app = new Module();

        $app->setState(Module::STATE_DONE);

        $res = $app->call();

        $this->assertInstanceOf('Server\Response', $res);
    }
}
