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
}
