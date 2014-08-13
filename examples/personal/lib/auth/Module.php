<?php

namespace Auth;

use Server\Module as Base;
use Server\LayerInterface;
use Server\Request;
use Server\Error;

class Module extends Base
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);

        $this->map(array(
            'method' => 'GET',
            'pattern' => '/login',
            'controller' => 'Auth\Controller',
            'action' => 'showLoginPage'
        ));

        $this->map(array(
            'method' => 'POST',
            'pattern' => '/login',
            'controller' => 'Auth\Controller',
            'action' => 'login'
        ));
    }

    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        if ($req->session['isLoggedIn']) {
            $res->data['menu']->addActionItem(array(
                'uri' => '/auth/logout',
                'label' => _('Log out')
            ));
        } else {
            $res->data['menu']->addActionItem(array(
                'uri' => '/auth/login',
                'label' => _('Log in')
            ));
        }

        return $res;
    }
}
