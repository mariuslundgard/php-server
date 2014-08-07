<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class RequestMatcherTest extends Base
{
    public function testMatchWildcard()
    {
        $req = new Request('GET', '/testing/1/2/3');

        $params = array(
            'pattern' => '*'
        );

        $this->assertEquals(array(
            'uri' => '/testing/1/2/3'
        ), RequestMatcher::matches($req, $params));
    }

    public function testMatchNamedParams()
    {
        $req = new Request('GET', '/testing/1/2/3');

        $params = array(
            'pattern' => '/:name/:id1/:id2/:id3',
        );

        $this->assertEquals(array(
            'name' => 'testing',
            'id1' => '1',
            'id2' => '2',
            'id3' => '3',
        ), RequestMatcher::matches($req, $params));
    }

    public function testMatchNamedWildcard()
    {
        $req = new Request('GET', '/testing/1/2/3');

        $params = array(
            'pattern' => '/testing/*path',
        );

        $this->assertEquals(array(
            'path' => '1/2/3',
        ), RequestMatcher::matches($req, $params));
    }

    // public function testMatchNamedRegex()
    // {
    //     $req = new Request('GET', '/testing/1/2/3');

    //     $params = array(
    //         'pattern' => '/testing/:id:<[^.]+>',
    //     );

    //     $this->assertEquals(array(
    //         'path' => '1/2/3',
    //     ), RequestMatcher::matches($req, $params));
    // }
}
