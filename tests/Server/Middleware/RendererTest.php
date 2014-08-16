<?php

namespace Server\Middleware;

use PHPUnit_Framework_TestCase as Base;
use Server\Layer;
use Server\Request;
use Server\Error;

class TestApp extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->type = 'text/html';
        $res->data['view'] = $this->config['view'];

        return $res;
    }
}

class ResponseWriter extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->write('test');

        return $res;
    }
}

class JsonResponseWriter extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->type = 'application/json';
        $res->data->set($this->config->get('data'));

        return $res;
    }
}

class EmptyPlainTextApp extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->type = 'text/plain';

        return $res;
    }
}

class NonexistentResponeTypeLayer extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->type = 'text/custom';

        return $res;
    }
}

class RendererTest extends Base
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
        $app = new Renderer(new TestApp(null, array('view' => 'test.tpl')));
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsNonexistentViewPathError()
    {
        $app = new Renderer(new TestApp(null, array('view' => 'test.tpl')), array(
            'viewPath' => __DIR__.'/../nonexistent'
        ));
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsNonexistentViewPathNameError()
    {
        $app = new Renderer(new TestApp(), array(
            'viewPath' => dirname(__DIR__).'/lib',
            'defaultView' => 'nonexistent.tpl'
        ));
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);
    }

    public function testViewRender()
    {
        $app = new Renderer(new TestApp(), array(
            'viewPath' => dirname(__DIR__).'/lib',
            'defaultView' => 'test.tpl'
        ));
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);

        $this->assertEquals('TEST VIEW', $res->body);
    }

    public function testViewAndLayoutRender()
    {
        $app = new Renderer(new TestApp(), array(
            'viewPath' => dirname(__DIR__).'/lib',
            'layoutPath' => dirname(__DIR__).'/lib',
            'defaultView' => 'test.tpl',
            'defaultLayout' => 'layout.tpl'
        ));
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);

        $this->assertEquals('TEST VIEW WITH A WRAPPING LAYOUT VIEW', $res->body);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testThrowsNonexistentLayoutPathNameError()
    {
        $app = new Renderer(new TestApp(), array(
            'viewPath' => dirname(__DIR__).'/lib',
            'layoutPath' => dirname(__DIR__).'/lib',
            'defaultView' => 'test.tpl',
            'defaultLayout' => 'nonexistent.tpl'
        ));
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);
    }

    public function testRenderSkipsAlreadyWrittedResponse()
    {
        $app = new Renderer(new ResponseWriter());
        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);

        $this->assertEquals(4, $res->length);
    }

    public function testRenderJsonResponse()
    {
        $app = new Renderer(new JsonResponseWriter(null, array(
            'data' => array( 'test' => true )
        )));

        $req = new Request('GET', '/foo/bar');
        $res = $app->call($req);

        $this->assertEquals('application/json', $res->type);
        $this->assertEquals("{\n    \"test\": true\n}", $res->body);
    }

    public function testRenderEmptyPlainText()
    {
        $app = new Renderer(new EmptyPlainTextApp());

        $req = new Request('GET', '/foo/bar');

        $res = $app->call($req);

        $this->assertEquals('text/plain', $res->type);
        $this->assertEquals('', $res->body);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testRenderUnsupportedResponseType()
    {
        $app = new Renderer(new NonexistentResponeTypeLayer());

        $req = new Request('GET', '/foo/bar', array(), array(
            'Content-Type' => 'application/hal+json'
        ));

        $res = $app->call($req);
    }
}
