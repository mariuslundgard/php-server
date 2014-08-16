<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class HttpParserTest extends Base
{
    public function testKeyValuePair()
    {
        $ret = HttpParser::parseDict('max-age=0');

        $this->assertEquals(array(
            'max-age' => 0
        ), $ret);
    }

    public function testKeyOnly()
    {
        $ret = HttpParser::parseDict('private');

        $this->assertEquals(array(
            'private' => null
        ), $ret);
    }

    public function testMixedValues()
    {
        $ret = HttpParser::parseDict('private, max-age=0, no-cache');

        $this->assertEquals(array(
            'private' => null,
            'max-age' => 0,
            'no-cache' => null
        ), $ret);
    }

    public function testQuotedString()
    {
        $ret = HttpParser::parseDict('test="quoted string"');

        $this->assertEquals(array(
            'test' => 'quoted string'
        ), $ret);
    }

    public function testEscapedQuotedString()
    {
        $ret = HttpParser::parseDict('test="quoted \"string\""');

        $this->assertEquals(array(
            'test' => 'quoted "string"'
        ), $ret);
    }
}
