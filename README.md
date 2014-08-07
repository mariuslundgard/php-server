php-server
=========

[![Build Status](https://travis-ci.org/mariuslundgard/php-server.svg?branch=develop)](https://travis-ci.org/mariuslundgard/php-server)
[![Coverage Status](https://img.shields.io/coveralls/mariuslundgard/php-server.svg)](https://coveralls.io/r/mariuslundgard/php-server?branch=develop)

```php
use Server\Stack as App;

$app = new App();

$app->employ([
	'class' => 'CustomLayer',
]);

$res = $app->call(new Server\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
));
```
