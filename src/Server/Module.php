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

        $topLevelApp = $this->getTopLevelApp();

        if (static::STATE_DONE !== $topLevelApp->getState() && (! $this->next && static::STATE_LOOP === $this->state || $this->next)) {
            foreach ($this->routes as $params) {
                $params += array( 'pattern' => null );
                $matchParams = array();

                if (! $params['pattern'] || is_array($matchParams = RequestMatcher::matches($req, $params))) {
                    $topLevelApp->setState(static::STATE_DONE);
                    $res = $this->next ? $this->next->call($req, $err) : parent::call($req, $err);
                    $this->callAction($req, $res, $params, $matchParams);

                    return $res;
                }
            }
        }

        return $this->next ? $this->next->call($req, $err) : parent::call($req, $err);
    }

    public function map(array $params)
    {
        $this->routes[] = $params;

        return $this;
    }

    public function callAction(Request $req, Response $res, array $params, array $matchParams)
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
    }
}
