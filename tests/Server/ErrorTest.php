<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class ErrorTest extends Base
{
    public function testDefault()
    {
        $err = new Error('Test `Internal error` error');

        $this->assertInstanceOf('Server\Error', $err);
        $this->assertEquals($err->getCode(), 500);
    }

    public function test404()
    {
    	$err = new Error('Testing `Not found` error');

        $this->assertInstanceOf('Server\Error', $err);
        $this->assertEquals($err->getCode(), 500);
    }
}
