<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class TestStack extends Stack
{
    public function call(Request $req = null, Error $err = null)
    {
        $res = $this->next ? $this->next->call($req, $err) : parent::call($req, $err);

        $res->write($this->config['body'] ? $this->config['body'] : 'test-layer');

        return $res;
    }
}

class StackTest extends Base
{
    public function testCreate()
    {
        $stack = new Stack();

        $this->assertInstanceOf('Server\Stack', $stack);
    }

    public function testGetters()
    {
        $app = new Stack();
        $this->assertEquals(Stack::STATE_IDLE, $app->state);
        $this->assertInstanceOf('SplStack', $app->stack);
        $this->assertInstanceOf('Util\Dictionary', $app->config);
    }

    public function testCallEmptyStack()
    {
        $stack = new Stack();

        $this->assertInstanceOf('Server\Response', $stack->call());

        $stack->setState(Stack::STATE_DONE);

        $this->assertInstanceOf('Server\Response', $stack->call());

        $this->assertNull($stack->resolve());
    }

    public function testEmploy()
    {
        $stack = new Stack();

        $stack->employ(array(
            'class' => 'Server\TestStack'
        ));

        $this->assertEquals($stack->count(), 1);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testEmployNonexistingClassThrowsError()
    {
        $stack = new Stack();

        $stack->employ(array(
            'class' => 'TestStack'
        ));

        $app = $stack->resolve();
    }

    /**
     * @expectedException     Server\Error
     */
    public function testEmployInsuffientFrameParametersThrowsError()
    {
        $stack = new Stack();

        $stack->employ(array());

        $app = $stack->resolve();
    }

    public function testResolve()
    {
        $stack = new Stack();

        $stack->employ(array(
            'class' => 'Server\TestStack'
        ));

        $app = $stack->resolve();

        $this->assertInstanceOf('Server\Stack', $app);
    }

    public function testEmployAndResolveInstance()
    {
        $stack = new Stack();

        $stack->employ(array(
            'instance' => new TestStack(),
        ));

        $app = $stack->resolve();
    }

    public function testCallsTestLayer()
    {
        $stack = new Stack();

        $expectedBody = 'test-resolve';

        $stack->employ(array(
            'class' => 'Server\TestStack',
            'config' => array(
                'body' => 'test-resolve'
            )
        ));

        $res = $stack->call();

        $this->assertEquals($res->body, 'test-resolve');
    }

    public function testResolveBasedOnUriPath()
    {
        $stack = new Stack();

        $stack->employ(array(
            'pattern' => '/foo*',
            'class' => 'Server\TestStack',
            'config' => array( 'body' => 'foo' )
        ));

        $stack->employ(array(
            'pattern' => '/bar*',
            'class' => 'Server\TestStack',
            'config' => array( 'body' => 'bar' )
        ));

        $req = new Request('GET', '/foo');

        $app = $stack->resolve($req);

        $res = $app->call($req);

        $this->assertEquals('foo', $res->body);
    }
}
