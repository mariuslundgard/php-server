<?php

namespace About;

use Server\Module as Base;
use Server\LayerInterface;

class Module extends Base
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);

        $this->map(array(
            'pattern' => '*',
            'fn' => function ($req, $res) {
                $res->write($this->master->getHeader());
                $res->write($this->master->getMenuHtml());
                $res->write('<div class="article"><div class="article-header"><h1>About</h1></div>');
                $res->write($this->master->getFooter());
            }
        ));
    }
}
