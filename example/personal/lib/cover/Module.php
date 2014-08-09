<?php

namespace Cover;

use Server\Module as Base;
use Server\LayerInterface;

class Module extends Base
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);

        $this->map(array(
            'pattern' => '/',
            'fn' => function ($req, $res) {
                $pageTitle = 'Welcome to '.$this->master->config['title'].'!';
                $res->write($this->master->getHeader(/*$pageTitle*/));
                $res->write($this->master->getMenuHtml());
                $res->write('<div class="article"><div class="article-header"><h1>'.$pageTitle.'</h1></div></div>');
                $res->write($this->master->getFooter());
            }
        ));

        $this->map(array(
            'pattern' => '*',
            'fn' => function ($req, $res) {
                $pageTitle = '404 — Not Found';
                $res->write($this->master->getHeader($pageTitle));
                $res->write($this->master->getMenuHtml());
                $res->write('<div class="article"><div class="article-header"><h1>'.$pageTitle.'</h1></div></div>');
                $res->write($this->master->getFooter());
            }
        ));
    }
}
