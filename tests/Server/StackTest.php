<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class TestStack extends Stack
{
	public function call(Request $req = null, Error $err = null)
	{
		$res = parent::call($req, $err);

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

    public function testEmploy()
    {
    	$stack = new Stack();

    	$stack->employ([
    		'class' => 'Server\TestStack'
    	]);

    	$this->assertEquals($stack->count(), 1);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testEmployNonexistingClassThrowsError()
    {
    	$stack = new Stack();

    	$stack->employ([
    		'class' => 'TestStack'
    	]);

    	$app = $stack->resolve();
    }

    /**
     * @expectedException     Server\Error
     */
    public function testEmployInsuffientFrameParametersThrowsError()
    {
    	$stack = new Stack();

    	$stack->employ([]);

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

        $stack->employ([
            'instance' => new TestStack(),
        ]);

        $stack->resolve();
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

    	$app = $stack->resolve();

    	$res = $app->call();

    	$this->assertEquals($res->body, 'test-resolve');

    	// $this->assertInstanceOf('Server\Stack', $app);
    }

    public function testResolveBasedOnUriPath()
    {
    	$stack = new Stack();

    	$stack->employ([
    		'pattern' => '/foo*',
    		'class' => 'Server\TestStack',
    		'config' => [ 'body' => 'foo' ],
    	]);

    	$stack->employ([
    		'pattern' => '/bar*',
			'class' => 'Server\TestStack',
			'config' => [ 'body' => 'bar' ],
    	]);

    	$req = new Request('GET', '/foo');

    	$app = $stack->resolve($req);

    	$res = $app->call($req);

    	$this->assertEquals('foo', $res->body);
    }
}
