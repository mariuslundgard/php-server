<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class RequestTest extends Base
{
    public function testCreate()
    {
        $req = new Request('GET', '/test');

        $this->assertInstanceOf('Server\Request', $req);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsErrorOnNonexistingProperty()
    {
        $req = new Request('GET', '/test');

        $nonexisting = $req->nonexisting;
    }

    public function testGetData()
    {
        $req = new Request('GET', '/test');

        $this->assertTrue(is_array($req->data->get()));
    }

    public function testGetHeaders()
    {
        $req = new Request('GET', '/test');

        $this->assertTrue(is_array($req->headers->get()));
    }

    public function testSetCustomProperty()
    {
        $req = new Request('GET', '/test');

        $req->custom = 'test';

        $this->assertEquals('test', $req->custom);
    }

    public function testGetQuery()
    {
        $req = new Request('GET', '/foo/bar?message=test');

        $this->assertEquals('message=test', $req->query);
    }

    public function testIsAjax()
    {
        $req = new Request('GET', '/foo/bar');

        $this->assertFalse($req->isAjax());

        $ajaxReq = new Request('GET', '/foo/bar', array(), array(
            'X-Requested-With' => 'xmlhttprequest'
        ));

        $this->assertTrue($ajaxReq->isAjax());
    }
}
