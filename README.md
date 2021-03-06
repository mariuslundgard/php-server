php-server
==========

[![Build Status](https://travis-ci.org/mariuslundgard/php-server.svg)](https://travis-ci.org/mariuslundgard/php-server)
[![Coverage Status](https://coveralls.io/repos/mariuslundgard/php-server/badge.png)](https://coveralls.io/r/mariuslundgard/php-server)

[![Latest Stable Version](https://poser.pugx.org/mariuslundgard/php-server/v/stable.png)](https://packagist.org/packages/mariuslundgard/php-server)


### Features

* Routable middleware (application layers)
* Routable controller actions


### Examples

This is the canonical *Hello World* example:

```php
<?php 

require 'vendor/autoload.php';

$app = (new Server\Module())
	->map([
		'fn' => function ($req, $res) {
			$res->write('<h1>Hello, world</h1>');
		}
	])
	->call()  // calls the application
	->send(); // outputs the headers and response body
```

This example shows how to use middleware and map controllers:

```php
<?php

require 'vendor/autoload.php';

class HeaderFilter extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->body = '<h1>'.$res->body.'</h1>';

        return $res;
    }
}

class FrontController extends Server\Controller
{
    public function index()
    {
        return 'Hello, world!';
    }
}

$app = new Server\Module();

$app->employ([
    'class' => 'HeaderFilter',
]);

$app->map([
    'controller' => 'FrontController',
]);

$app->call()->send(); // outputs: <h1>Hello, world!</h1>
