<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class LayerTest extends Base
{
    public function testCreate()
    {
        $layer = new Layer();

        $this->assertInstanceOf('Server\Layer', $layer);
    }

    public function testHandleRequest()
    {
        $layer = new Layer();

        $res = $layer->call(new Request('GET', '/test'));

        $this->assertInstanceOf('Server\Response', $res);
    }

    public function testNestedHandleRequest()
    {
        $layer = new Layer(new Layer());

        $res = $layer->call(new Request('GET', '/test'));

        $this->assertInstanceOf('Server\Response', $res);
    }

    public function testSetApp()
    {
        $layer = new Layer();

        // $res = $layer->call(new Request('GET', '/test'));

        // $this->assertInstanceOf('Server\Response', $res);

        $layer->setApp(new Layer);
    }

    public function testGetCurrentRequest()
    {
        $layer = new Layer(null, array(), array(
            'HTTP_ACCEPT_LANGUAGE' => 'en'
        ));

        $req = $layer->getCurrentRequest();

        // $nonexisting = $req->nonexisting;

        $this->assertInstanceOf('Server\Request', $req);

        $this->assertEquals('en', $req->headers['Accept-Language']);
    }

    public function testGetNextResponse()
    {
        $layer = new Layer();

        $res = $layer->getNextResponse();

        $this->assertInstanceOf('Server\Response', $res);
    }

    public function testGetAndSetApp()
    {
        $layer = new Layer();

        $this->assertNull($layer->getApp());

        $layer->setApp(new Layer());

        $this->assertInstanceOf('Server\Layer', $layer->getApp());
    }

    public function testGetAndSetNext()
    {
        $layer = new Layer();

        $this->assertNull($layer->getNext());

        $layer->setNext(new Layer());

        $this->assertInstanceOf('Server\Layer', $layer->getNext());
    }

    public function testDump()
    {
        $layer = new Layer();

        $this->assertEquals(array(
            'class' => 'Server\Layer',
            'next' => null,
            'config' => array()
        ), $layer->dump());
    }
}
