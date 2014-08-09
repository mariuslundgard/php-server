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

    public function testGetters()
    {
        $layer = new Layer();

        $this->assertEquals(null, $layer->next);
        $this->assertInstanceOf('Util\Dictionary', $layer->config);
        $this->assertInstanceOf('Util\Dictionary', $layer->env);
        $this->assertEquals(null, $layer->app);
        $this->assertInstanceOf('Server\Layer', $layer->master);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testNonexistentProperty()
    {
        $layer = new Layer();

        $nonexisting = $layer->nonexisting;
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

        $layer->setApp(new Layer);
    }

    public function testGetCurrentRequest()
    {
        $layer = new Layer(null, array(), array(
            'HTTP_ACCEPT_LANGUAGE' => 'en'
        ));

        $req = $layer->getCurrentRequest();

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

    public function testGetCurrentUri()
    {
        $layer = new Layer(null, array(), array('REQUEST_URI' => '/test/foo/bar'));

        $this->assertEquals('/test/foo/bar', $layer->getCurrentUri());
    }

    public function testGetCurrentUriWithBasePath()
    {
        $layer = new Layer(null, array('basePath' => '/test'), array('REQUEST_URI' => '/test/foo/bar'));

        $this->assertEquals('/foo/bar', $layer->getCurrentUri());
    }

    /**
     * @expectedException     Server\Error
     */
    public function testGetCurrentUriWithInvalidBasePath()
    {
        $layer = new Layer(null, array('basePath' => '/foo'), array('REQUEST_URI' => '/test/foo/bar'));

        $uri = $layer->getCurrentUri();
    }

    public function testFactoryCreate()
    {
        $layer = Layer::create();

        $this->assertInstanceOf('Server\Layer', $layer);
    }
}
