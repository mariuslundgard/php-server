<?php

namespace Server;

use Util\Dictionary;
use Debug\DebuggableTrait;

class Layer implements LayerInterface
{
    use DebuggableTrait;

    protected $next;
    protected $config;
    protected $env;
    protected $app;

    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        $this->next = $next;
        $this->config = new Dictionary($config);
        $this->env = new Dictionary($env);
    }

    public function configure(array $config)
    {
        $this->config->merge($config);

        return $this;
    }

    public function isCallable()
    {
        return true;
    }

    public function call(Request $req, Error $err = null)
    {
        $this->d('Layer.call(`'.$req->method.' '.$req->uri.'`)');

        return $this->getNextResponse($req, $err);
    }

    public function getCurrentRequest()
    {
        $headers = array();
        foreach ($this->env->get() as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }

        return new Request(
            $this->env->get('REQUEST_METHOD', 'GET'),
            $this->env->get('REQUEST_URI', '/'),
            ('POST' === $this->env['REQUEST_METHOD'] ? $_POST : $_GET),
            $headers
        );
    }

    public function getNextResponse(Request $req = null, Error $err = null)
    {
        if ($next = $this->getNextCallable()) {
            return $next->call($req, $err);
        }

        if (! $req) {
            $req = $this->getCurrentRequest();
        }

        return new Response($req);
    }

    public function getApp()
    {
        return $this->app;
    }

    public function setApp(LayerInterface $app)
    {
        $this->app = $app;
    }

    public function getTopLevelApp()
    {
        return $this->next ? $this->next->getTopLevelApp() : $this;
    }

    public function getNext()
    {
        return $this->next;
    }

    public function getNextCallable()
    {
        if ($this->next) {
            return $this->next->isCallable() ? $this->next : $this->next->getNextCallable();
        }

        return null;
    }

    public function setNext(LayerInterface $next)
    {
        $this->next = $next;
    }

    public function dump()
    {
        return array(
            'class' => get_class($this),
            'next' => $this->next ? $this->next->dump() : null
        );
    }
}
