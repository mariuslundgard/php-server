<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class ApplicationTest extends Base
{
    public function testCreate()
    {
        $app = Application::create();

        $this->assertInstanceOf('Server\Application', $app);
    }

    public function testStaticUrl()
    {
        $app = Application::create(null, array(
            'baseStaticUrl' => 'http://localhost/'
        ));

        $this->assertEquals('http://localhost/test.css', $app->staticUrl('test.css'));

        $app2 = Application::create();

        $this->assertEquals('/test.css', $app2->staticUrl('test.css'));

        $this->assertEquals('http://localhost/test.css', $app->staticUrl('http://localhost/test.css'));
    }
}
