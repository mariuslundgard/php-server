<?php

// putenv('DEBUG=1');

define('APP_PATH', __DIR__);

require APP_PATH.'/../../vendor/autoload.php';
require APP_PATH.'/lib/cover/Module.php';
require APP_PATH.'/lib/blog/Module.php';
require APP_PATH.'/lib/blog/Controller.php';
require APP_PATH.'/lib/about/Module.php';
require APP_PATH.'/lib/menu/Layer.php';
require APP_PATH.'/lib/menu/View.php';

class Application extends Server\Application
{
    public function __construct(Server\LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config, $env);

        $this

            // employ middleware:
            ->employ([
                'class' => 'Server\Middleware\Renderer',
                'config' => [
                    'layoutPath' => __DIR__.'/lib',
                    'viewPath' => __DIR__.'/lib',
                    'defaultLayout' => 'layout'
                ]
            ])

            // employ application modules:
            ->employ(['class' => 'About\Module', 'pattern' => '/about*path'])
            ->employ(['class' => 'Blog\Module', 'pattern' => '/blog*path'])
            ->employ(['class' => 'Cover\Module'])

            ->employ([
                'class' => 'Menu\Layer',
                'config' => array(
                    'items' => array(
                        array('uri' => '/', 'label' => $this->config['title']),
                        array('uri' => '/blog', 'label' => 'Blog'),
                        array('uri' => '/about', 'label' => 'About')
                    )
                )
            ]);
    }
}

Application::create(null, [
    'title' => 'A Personal Site',
    'basePath' => '/~mariuslundgard/php-server/example/personal'
], $_SERVER)->call()->send();
