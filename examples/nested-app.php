<?php

putenv('DEBUG=1');

require __DIR__.'/../vendor/autoload.php';

class UriWriterModule extends Server\Module
{
    public function call(Server\Request $req = null, Server\Error $err = null)
    {
        $this->d('CALL - DUMP: ', $this->dump());

        $res = parent::call($req, $err);

        $res->write($this->config->get('uri', $req->uri));

        return $res;
    }
}

$app = new Server\Module();
$mod1 = new UriWriterModule();
$mod2 = new UriWriterModule();

$mod2->employ(array( 'pattern' => '/test3*uri', 'class' => 'UriWriterModule' ));
$mod1->employ(array( 'pattern' => '/test2*uri', 'instance' => $mod2 ));
$app->employ(array( 'pattern' => '/test1*uri', 'instance' => $mod1 ));

// $mod1 = new UriWriterModule();
// // $mod2 = new UriWriterModule();

// $mod1// = (new Server\Module())
//     ->employ(array( 'pattern' => '/test2*', 'class' => 'UriWriterModule' ));

// $app = (new Server\Module())
//     ->employ(array( 'pattern' => '/test1*', 'instance' => $mod1 ));

$res = $app->call(new Server\Request('GET', '/test1/test2/test3/hello'));

// echo $res->body;
$res->send();


// d($app->resolve(new Server\Request('GET', '/test1/test2/test3'))->dump());
