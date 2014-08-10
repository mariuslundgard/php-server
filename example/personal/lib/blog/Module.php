<?php

namespace Blog;

use Server\Module as Base;
use Server\LayerInterface;

class Module extends Base
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);


        $this->map(array(
            'pattern' => '/',
            'controller' => 'Blog\Controller'
        ));

        $this->map(array(
            'pattern' => '*',
            'controller' => 'Blog\Controller',
            'action' => 'read'
        ));
    }
}
