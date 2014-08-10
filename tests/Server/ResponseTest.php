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
        $this->assertTrue(is_array($res->headers->get()));
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
        $output = $res->send('', false);

        $this->assertEquals('', $output);
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

    public function testGetLength()
    {
        $res = new Response(new Request('GET', '/'));

        $this->assertEquals(0, $res->length);

        $res->write('foo');

        $this->assertEquals(3, $res->length);
    }

    public function testStatus()
    {
        $res = new Response(new Request('GET', '/'));

        $this->assertEquals(200, $res->status);
        $this->assertEquals('200 OK', $res->statusMessage);

        $res->status = 304;

        $this->assertEquals(304, $res->status);
        $this->assertEquals('304 Not Modified', $res->statusMessage);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testSetInvalidStatusCodeThrowsError()
    {
        $res = new Response(new Request('GET', '/'));

        $res->status = 0;
    }

    public function testGetStatusHeader()
    {
        $res = new Response(new Request('GET', '/'));

        $this->assertEquals('HTTP/1.1 200 OK', $res->getStatusHeader());

        $res->status = 304;

        $this->assertEquals('HTTP/1.1 304 Not Modified', $res->getStatusHeader());
    }
}
