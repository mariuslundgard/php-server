<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class UriWriterModule extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        // $this->d('call');

        $res = parent::call($req, $err);

        // $res->write($req->uri);
        $res->write($this->config['uri']);

        return $res;
    }
}

$app = new Server\Module();
$mod1 = new UriWriterModule();

$mod1->employ(array( 'pattern' => '/test2*uri', 'class' => 'UriWriterModule' ));
$app->employ(array( 'pattern' => '/test1*uri', 'instance' => $mod1 ));

// $mod1 = new UriWriterModule();
// // $mod2 = new UriWriterModule();

// $mod1// = (new Server\Module())
//     ->employ(array( 'pattern' => '/test2*', 'class' => 'UriWriterModule' ));

// $app = (new Server\Module())
//     ->employ(array( 'pattern' => '/test1*', 'instance' => $mod1 ));

$res = $app->call(new Server\Request('GET', '/test1/test2/test3'));

echo $res->body;


// d($app->resolve(new Server\Request('GET', '/test1/test2/test3'))->dump());
