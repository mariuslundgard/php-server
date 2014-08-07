<?php

namespace Server;

use PHPUnit_Framework_TestCase as Base;

class RequestMatcherTest extends Base
{
	public function testMatchesWildcard()
	{
		$req = new Request('GET', '/testing/1/2/3');

		$params = array(
			'pattern' => '*',
		);

		$this->assertEquals(RequestMatcher::matches($req, $params), array(
			'uri' => '/testing/1/2/3'
		));
	}
}
