php-server
==========

[![Build Status](https://travis-ci.org/mariuslundgard/php-server.svg?branch=develop)](https://travis-ci.org/mariuslundgard/php-server)
[![Coverage Status](https://coveralls.io/repos/mariuslundgard/php-server/badge.png?branch=develop)](https://coveralls.io/r/mariuslundgard/php-server?branch=develop)

```php
<?php

require 'vendor/autoload.php';

use Server\Stack as App;

class CustomMiddleware extends Server\Layer
{
	public function call(Server\Request $req = null, Server\Error $err = null)
	{
		$res = parent::call($req, $err);

		$res->write('<h1>Hello, world!</h1>');

		return $res;
	}
}

$app = new App();

$app->employ([
	'class' => 'CustomMiddleware',
]);

$res = $app->call(new Server\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
));

$res->send(); // outputs: <h1>Hello, world!</h1>
```
