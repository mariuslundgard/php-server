<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

$app = (new Server\Module())
    ->map([
        'fn' => function ($req, $res) {
            $res->write('<h1>Hello, world</h1>');
        }
    ])
    ->call()  // calls the application
    ->send(); // outputs the headers and response body
