<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

$app = new Server\Stack();

$app->employ([
	'class' => 'Gzip'
]);

$res = $app->call(new Server\Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']));

$res->send();

d($res);
