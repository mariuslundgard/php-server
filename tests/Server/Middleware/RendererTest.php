<?php

namespace Server\Middleware;

use PHPUnit_Framework_TestCase as Base;
use Server\Request;

class ErrorTest extends Base
{
    public function testCreate()
    {
        $app = new Renderer();
        $this->assertInstanceOf('Server\Middleware\Renderer', $app);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsMissingParameterError()
    {
        $app = new Renderer();

        $res = $app->call(new Request('GET', '/foo/bar'));
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsNonexistentViewPathError()
    {
        $app = new Renderer(null, array(
            'viewPath' => __DIR__.'/../nonexistent'
        ));

        $res = $app->call(new Request('GET', '/foo/bar'));
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsNonexistentViewPathNameError()
    {
        $app = new Renderer(null, array(
            'viewPath' => __DIR__.'/../lib',
            'defaultView' => 'nonexistent.tpl'
        ));

        $res = $app->call(new Request('GET', '/foo/bar'));
    }

    public function testViewRender()
    {
        $app = new Renderer(null, array(
            'viewPath' => __DIR__.'/../lib',
            'defaultView' => 'test.tpl'
        ));

        $res = $app->call(new Request('GET', '/foo/bar'));

        $this->assertEquals('TEST VIEW', $res->body);
    }

    public function testViewAndLayoutRender()
    {
        $app = new Renderer(null, array(
            'viewPath' => __DIR__.'/../lib',
            'layoutPath' => __DIR__.'/../lib',
            'defaultView' => 'test.tpl',
            'defaultLayout' => 'layout.tpl'
        ));

        $res = $app->call(new Request('GET', '/foo/bar'));

        $this->assertEquals('TEST VIEW WITH A WRAPPING LAYOUT VIEW', $res->body);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsNonexistentLayoutPathNameError()
    {
        $app = new Renderer(null, array(
            'viewPath' => __DIR__.'/../lib',
            'layoutPath' => __DIR__.'/../lib',
            'defaultView' => 'test.tpl',
            'defaultLayout' => 'nonexistent.tpl'
        ));

        $res = $app->call(new Request('GET', '/foo/bar'));
    }

}
