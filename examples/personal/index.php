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
            ->employ(['class' => 'Server\Middleware\Accept'])
            ->employ([
                'class' => 'Server\Middleware\L10n',
                'config' => [
                    'domainPath' => APP_PATH.'/locale'
                ]
            ])
            ->employ(['class' => 'Server\Middleware\Encoding'])
            ->employ(['class' => 'Server\Middleware\Session'])
            ->employ(['class' => 'Server\Middleware\Cookie'])
            ->employ(['class' => 'Server\Middleware\UAParser'])
            ->employ(['class' => 'Server\Middleware\Safari304Workaround'])
            ->employ([
                'class' => 'Server\Middleware\Cache',
                'config' => [
                    'dirPath' => APP_PATH.'/cache',
                    'pattern' => '{:scheme}://{:host}{:uri}/{:locale}'
                ],
            ])
            ->employ([
                'class' => 'Server\Middleware\Renderer',
                'config' => [
                    'layoutPath' => APP_PATH.'/lib',
                    'viewPath' => APP_PATH.'/lib',
                    'defaultLayout' => 'layout'
                ]
            ])
            ->employ(['class' => 'Server\Middleware\ErrorHandler'])

            // employ application modules:
            ->employ(['class' => 'About\Module', 'pattern' => '/about*path'])
            ->employ(['class' => 'Blog\Module', 'pattern' => '/blog*path'])
            ->employ(['class' => 'Cover\Module'])

            ->employ([
                'class' => 'Menu\Layer',
                'config' => array(
                    'items' => array(
                        array('uri' => '/', 'label' => $this->config['title']),
                        array('uri' => '/blog', 'label' => _('Blog')),
                        array('uri' => '/about', 'label' => _('About'))
                    )
                )
            ]);
    }
}

Application::create(null, [
    'title' => _('A Personal Site'),
    'basePath' => '/examples/personal'
], $_SERVER)->call()->send();
