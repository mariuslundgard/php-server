<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

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
}
