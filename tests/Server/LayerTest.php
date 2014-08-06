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
}
