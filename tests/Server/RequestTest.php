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
}
