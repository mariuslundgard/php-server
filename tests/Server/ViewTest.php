<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class ViewTest extends Base
{
    public function testCreate()
    {
        $view = View::create(__DIR__.'/lib/test.tpl');

        $this->assertInstanceOf('Server\View', $view);
    }

    /**
     * @expectedException     Server\Error
     */
    public function testNonexistentPathThrowsErrorOnRender()
    {
        $view = View::create(__DIR__.'/lib/nonexistent.tpl');

        $view->render();
    }
}
