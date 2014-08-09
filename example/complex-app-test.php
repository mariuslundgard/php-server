<?php

$header = '<div style="padding: 1em; background: #fe0;"><div class="nav"><a href="?uri=/">Front</a> <a href="?uri=/blog">Blog</a> <a href="?uri=/info">Info</a></div>';
$footer = '</div>';

//////////////////////////////////////////////////////////////////////////////
putenv('DEBUG=1');
require __DIR__.'/../vendor/autoload.php';

//////////////////////////////////////////////////////////////////////////////
class Blog extends Server\Module
{
    public function __construct(Server\LayerInterface $next, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);

        $this->map(array(
            'pattern' => '*',
            'fn' => function ($req, $res) {
                // d('blog');
                return '<h1>Blog</h1>';
            }
        ));
    }
}

//////////////////////////////////////////////////////////////////////////////
class Info extends Server\Module
{
    public function __construct(Server\LayerInterface $next, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);

        $this->map(array(
            'pattern' => '*',
            'fn' => function ($req, $res) {
                // d('info');
                return '<h1>Info</h1>';
            }
        ));
    }
}

//////////////////////////////////////////////////////////////////////////////
class Renderer extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->body = $this->config['header'].$res->body.$this->config['footer'];
        // $res->body = '<pre style="border: 3px solid #00f; padding: 1em;">rendered: '.$res->body.'</pre>';

        return $res;
    }
}

//////////////////////////////////////////////////////////////////////////////
class Compression extends Server\Layer
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $res = parent::call($req, $err);
        // $res->write('gzip:');
        $res->body = '<pre style="border: 3px solid #f00; padding: 1em;">'.$res->body.'</pre>';

        return $res;
    }
}

//////////////////////////////////////////////////////////////////////////////
$req = new Server\Request('GET', isset($_GET['uri']) ? $_GET['uri'] : '/');

$app = (new Server\Module())

    ->employ(array( 'class' => 'Compression' ))
    ->employ(array( 'class' => 'Renderer', 'config' => compact('header', 'footer')))

    ->employ(array( 'class' => 'Blog', 'pattern' => '/blog*uri' ))
    ->employ(array( 'class' => 'Info', 'pattern' => '/info*uri' ))

    ->map(array( 'pattern' => '/', 'fn' => function ($req, $res) { return '<h1>Front: '.$req->uri.'</h1>'; }))
    ->map(array( 'fn' => function ($req, $res) { return 'NOT FOUND: '.$req->uri; }))

    ->call($req)->send();
