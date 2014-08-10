<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class App extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        d('CALLING APP (#0)');

        $res = parent::call($req, $err);

        // $res->write('<p>URI: '.$req->uri.'</p>');

        return $res;
    }
}

class UriWriter extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        d('CALLING URI WRITER (#1)');

        $res = parent::call($req, $err);

        $res->write('<p>URI: '.$req->uri.'</p>');

        return $res;
    }
}

class News extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        d('CALLING NEWS (#2)');

        $res = parent::call($req, $err);

        // $res->write('<p>URI: '.$req->uri.'</p>');

        return $res;
    }
}

class Search extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        d('CALLING NEWS/SEARCH (#3)');

        $res = parent::call($req, $err);

        // $res->write('<p>URI: '.$req->uri.'</p>');

        return $res;
    }
}

$config = array(
    'name'  => 'auto-stack',
    'title' => 'Auto Stack',
);

$app = new App(null, $config, $_SERVER);

$app->employ(array(
    'class' => 'UriWriter'
));

$news = new News();

$app->employ(array( 'instance' => $news ));

$news->employ(array( 'class' => 'Search'));

$res = $app->call();

d($res);
