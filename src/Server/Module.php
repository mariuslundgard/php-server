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

    public function call(Request $req = null, Error $err = null)
    {
        if (! $req) {
            $req = new Request();
        }

        foreach ($this->routes as $params) {
            $params += array( 'pattern' => null );
            $matchParams = array();

            if (! $params['pattern'] || $matchParams = RequestMatcher::matches($req, $params)) {
                $res = $this->next ? $this->next->call($req, $err) : parent::call($req, $err);

                $data = $this->callAction($req, $res, $params, $matchParams);

                if (is_array($data)) {
                    $res->data->set($data);
                } elseif (is_string($data)) {
                    $res->write($data);
                }

                // TODO: tell application to not look for more routes
                return $res;
            }
        }

        return $this->next ? $this->next->call($req, $err) : parent::call($req, $err);
    }

    public function map(array $params)
    {
        $this->routes[] = $params;
    }

    public function callAction(Request $req, Response $res, array $params, array $matchParams)
    {
        $params += array( 'controller' => null, 'action' => 'index', 'fn' => null );

        if ($params['controller']) {

            if (! class_exists($params['controller'])) {
                throw new Error('The controller was not found: '.$params['controller']);
            }

            $refl = new ReflectionClass($params['controller']);
            $controller = $refl->newInstanceArgs([$this, $req, $res]);

            return call_user_func_array([$controller, $params['action']], $matchParams);

        } elseif ($params['fn']) {
            return call_user_func_array($params['fn'], array_merge([$req, $res], $matchParams));

        }

        throw new Error('Insufficient route parameters');
    }
}
