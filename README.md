php-server
=========

[![Build Status](https://travis-ci.org/mariuslundgard/php-server.png?branch=master)](https://travis-ci.org/mariuslundgard/php-server)
[![Coverage Status](https://coveralls.io/repos/mariuslundgard/php-server/badge.png)](https://coveralls.io/r/mariuslundgard/php-server)

```php
use Server\Stack as App;

$app = new App();

$app->employ([
	'class' => 'CustomLayer',
]);

$res = $app->call(new Server\Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']));
```
