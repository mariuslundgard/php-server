<?php

namespace Menu;

use Server\Layer as Base;
use Server\LayerInterface;
use Server\Request;
use Server\Error;

class Layer extends Base
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);
    }

    public function call(Request $req = null, Error $err = null)
    {
        $res = parent::call($req, $err);

        $res->data['menu'] = new View($this->master, $this->config->get('items', array()));

        $res->data['menu']->addMenuItem([
            'uri' => '/',
            'label' => time()
        ]);

        return $res;
    }
}
