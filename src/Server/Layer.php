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

    public function __get($property)
    {
        switch ($property) {

            case 'next':
                return $this->next;

            case 'config':
                return $this->config;

            case 'env':
                return $this->env;

            case 'app':
                return $this->app;

            case 'master':
                return $this->getMaster();

            default:
                throw new Error('Nonexisting layer property: '.$property);
        }
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
        $this->d('Layer.call('.($req ? '`'.$req->method.' '.$this->config->get('uri', $req->uri).'`' : 'NULL').')');

        return $this->getNextResponse($req, $err);
    }

    public function getCurrentRequest()
    {
        return new Request(
            $this->env->get('REQUEST_METHOD', 'GET'),
            $this->getCurrentUri(),
            $this->getCurrentData(),
            $this->getCurrentHeaders()
        );
    }

    public function getCurrentUri()
    {
        $uri = $this->env->get('REQUEST_URI', '/');

        if ($basePath = $this->config['basePath']) {
            $basePathLength = strlen($basePath);
            if ($basePath !== substr($uri, 0, $basePathLength)) {
                throw new Error('Invalid base path: '.$basePath);
            }
            $uri = substr($uri, $basePathLength);
        }

        return $uri;
    }

    public function getCurrentData()
    {
        return 'POST' === $this->env['REQUEST_METHOD'] ? $_POST : $_GET;
    }

    public function getCurrentHeaders()
    {
        $ret = array();
        foreach ($this->env->get() as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $ret[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }

        return $ret;
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

    public function getMaster()
    {
        return $this->next ? $this->next->getMaster() : $this;
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
            'next' => $this->next ? $this->next->dump() : null,
            'config' => $this->config->get()
        );
    }

    public static function create(LayerInterface $next = null, array $config = [], array $env = [])
    {
        return new static($next, $config, $env);
    }
}
