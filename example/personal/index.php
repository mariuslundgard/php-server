<?php

// putenv('DEBUG=1');

define('APP_PATH', __DIR__);

require APP_PATH.'/../../vendor/autoload.php';
require APP_PATH.'/lib/cover/Module.php';
require APP_PATH.'/lib/blog/Module.php';
require APP_PATH.'/lib/about/Module.php';

class Application extends Server\Application
{
    public function __construct(Server\LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config, $env);

        $this

            // employ middleware:
            ->employ(['class' => 'Server\Middleware\Renderer'])

            // employ application modules:
            ->employ(['class' => 'About\Module', 'pattern' => '/about*uri'])
            ->employ(['class' => 'Blog\Module', 'pattern' => '/blog*uri'])
            ->employ(['class' => 'Cover\Module'])
;
    }

    public function getMenuHtml()
    {
        return '<div class="xs-max-size"><div class="nav trinity rule-after">'
            .'<div class="nav-left">'
            .'<a class="button" href="'.$this->master->getRealPath('/').'">'.$this->config['title'].'</a>'
            .'<a class="button" href="'.$this->master->getRealPath('/blog').'">Blog</a>'
            .'<a class="button" href="'.$this->master->getRealPath('/about').'">About</a>'
            .'</div></div></div>';
    }

    public function getHeader($pageTitle = null)
    {
        return '<html><head><meta charset="utf-8"><title>'.(($pageTitle ? $pageTitle.' – ' : '').$this->config['title']).'</title><link rel="stylesheet" href="http://localhost/~mariuslundgard/body/dist/body.css"></head><body class="no-margin">';
    }

    public function getFooter()
    {
        return '</head></html>';
    }
}

Application::create(null, [
    'title' => 'A Personal Site',
    'basePath' => '/~mariuslundgard/php-server/example/personal'
], $_SERVER)->call()->send();
