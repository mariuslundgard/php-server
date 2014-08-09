<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class ResponseTest extends Base
{
    public function testCreate()
    {
        $res = new Response(new Request('GET', '/test'));

        $this->assertInstanceOf('Server\Response', $res);
    }

    public function testGetters()
    {
        $res = new Response(new Request('GET', '/test'));

        // $this->assertInstanceOf('Server\Response', $res);
        $this->assertTrue(is_array($res->headers));
        $this->assertInstanceOf('Util\Dictionary', $res->data);
        $this->assertEquals('text/html', $res->type);
    }

    public function testSetType()
    {
        $res = new Response(new Request('GET', '/test'));

        // $this->assertInstanceOf('Server\Response', $res);
        // $this->assertTrue(is_array($res->headers));
        // $this->assertInstanceOf('Util\Dictionary', $res->data);
        $res->type = 'application/json';

        $this->assertEquals('application/json', $res->type);
    }

    public function testWrite()
    {
        $res = new Response(new Request('GET', '/test'));

        $res->write('test');
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsErrorOnNonexistingProperty()
    {
        $res = new Response(new Request('GET', '/test'));

        $nonexisting = $res->nonexisting;
    }

    public function testSend()
    {
        $res = new Response(new Request('GET', '/test'));
        $res->send();
    }

    public function testSetBodyProperty()
    {
        $res = new Response(new Request('GET', '/test'));

        $res->body = 'test';

        $this->assertEquals('test', $res->body);
        // $res->send();
    }

    /**
     * @expectedException     Server\Error
     */
    public function testSetNonexistentProperty()
    {
        $res = new Response(new Request('GET', '/test'));

        $res->nonexisting = 'test';
    }

}
