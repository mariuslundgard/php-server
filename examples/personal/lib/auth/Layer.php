<?php

namespace Auth;

use Server\Layer as Base;
// use Server\LayerInterface;
use Server\Request;
use Server\Error;

class Layer extends Base
{
    public function call(Request $req = null, Error $err = null)
    {
        if (! $req->session) {
            throw new Error('The `auth` module depends on the `Session` middleware');
        }

        if ($authErr = $req->session->get('authError')) {
            $req->session->flash('authError', $authErr);
            $req->ignoreCache = true;
        }

        return parent::call($req, $err);

        // echo $res->data['menu'];
        // d($res->data->get());
        // exit;
    }

    //     parent::__construct($next, $config, $env);

    //     $this->map(array(
    //         'method' => 'GET',
    //         'pattern' => '/login',
    //         'controller' => 'Auth\Controller',
    //         'action' => 'showLoginPage'
    //     ));

    //     $this->map(array(
    //         'method' => 'POST',
    //         'pattern' => '/login',
    //         'controller' => 'Auth\Controller',
    //         'action' => 'login'
    //     ));
    // }
}
