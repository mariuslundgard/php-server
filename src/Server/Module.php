<?php

namespace Server;

use ReflectionClass;

class Module extends Stack
{
    protected $routes;

    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);

        $this->routes = [];
    }

    public function __get($property)
    {
        switch ($property) {

            case 'routes':
                return $this->routes;

            default:
                return parent::__get($property);
        }
    }

    public function call(Request $req = null, Error $err = null)
    {
        // $this->d('Module.call('.($req ? '`'.$req->method.' '.$this->config->get('path', $req->uri).'`' : 'NULL').')');

        switch ($this->state) {

            case static::STATE_IDLE:
                if (! $req) {
                    $req = $this->getCurrentRequest();
                }
                if ($app = $this->resolve($req)) {
                    return $app->call($req, $err);
                }

                return $this->getProcessedResponse($req, $err);

            case static::STATE_LOOP:
                return $this->getProcessedResponse($req, $err);
        }

        return parent::call($req, $err);
    }

    public function map(array $params)
    {
        $this->routes[] = $params;

        return $this;
    }

    public function getProcessedResponse(Request $req = null, Error $err = null)
    {
        // $this->d('Module.getProcessedResponse('.($req ? '`'.$req->method.' '.$this->config->get('path', $req->uri).'`' : 'NULL').')');

        $topLevelApp = $this->getMaster();

        if (static::STATE_DONE !== $topLevelApp->getState()) {
            foreach ($this->routes as $params) {
                $params += array( 'pattern' => null );
                $matchParams = array();
                if (! $params['pattern'] || is_array($matchParams = RequestMatcher::matches($req, $params, $this->config['path']))) {
                    // $this->d('MATCHING ROUTE ', $matchParams);
                    $topLevelApp->setState(static::STATE_DONE);

                    return $this->process($req, $this->getNextResponse($req, $err), $params, $matchParams);
                }
            }
        }

        return $this->getNextResponse($req, $err);
    }

    public function process(Request $req, Response $res, array $params, array $matchParams)
    {
        $params += array( 'controller' => null, 'action' => 'index', 'fn' => null );

        if ($params['controller']) {

            if (! class_exists($params['controller'])) {
                throw new Error('The controller was not found: '.$params['controller']);
            }

            // TODO: use Instantiator?
            $refl = new ReflectionClass($params['controller']);
            $controller = $refl->newInstanceArgs([$this, $req, $res]);

            $data = call_user_func_array([$controller, $params['action']], $matchParams);

        } elseif ($params['fn']) {
            $data = call_user_func_array($params['fn'], array_merge([$req, $res], $matchParams));
        } else {
            throw new Error('Insufficient route parameters');
        }

        if (is_array($data)) {
            $res->data->set($data);
        } elseif (is_string($data)) {
            $res->write($data);
        }

        return $res;
    }

    public function getRealPath($path)
    {
        $basePath = $this->config['basePath'] ? '/'.trim($this->config['basePath'], '/') : '';
        $path = '/' . trim($path, '/');

        return $basePath.$path;
    }
}
