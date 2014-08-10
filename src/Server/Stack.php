<?php

namespace Server;

use SplStack;
use ReflectionClass;

class Stack extends Layer
{
    const STATE_IDLE = 0;
    const STATE_LOOP = 1;
    const STATE_DONE = 2;

    protected $stack;
    protected $state;

    public function __construct(LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config, $env);

        $this->stack = new SplStack();
        $this->state = static::STATE_IDLE;
    }

    public function __get($property)
    {
        switch ($property) {

            case 'state':
                return $this->state;

            case 'stack':
                return $this->stack;

            default:
                return parent::__get($property);
        }
    }

    public function isCallable()
    {
        return null === $this->next || static::STATE_IDLE === $this->state && $this->next;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

        // debug
        $states = [ 'IDLE', 'LOOP', 'DONE' ];
        $stateName = $states[$state];
        $this->d('Stack.setState(`'.$stateName.'`)');

        return $this;
    }

    public function call(Request $req = null, Error $err = null)
    {
        if (! $req) {
            $req = $this->getCurrentRequest();
        }

        // $this->d('Stack.call(`'.$req->method.' '.$req->uri.'`)');

        switch ($this->state) {

            case static::STATE_IDLE:
                if ($app = $this->resolve($req)) {
                    return $app->call($req, $err);
                }

                return parent::call($req, $err);

            case static::STATE_LOOP:
                return $this->next ? $this->next->call($req, $err) : new Response($req);

            default:
                return parent::call($req, $err);
        }
    }

    public function employ(array $params)
    {
        $this->stack->push($params);

        return $this;
    }

    public function resolve(Request $req = null)
    {
        switch ($this->state) {

            case static::STATE_IDLE:
                $next = $this;
                $hasLayers = false;
                foreach ($this->stack as $params) {
                    $params += array( 'pattern' => null, 'class' => null, 'instance' => null, 'config' => array() );
                    $match = array();
                    if (! $params['pattern'] || is_array($match = RequestMatcher::matches($req, $params, $this->config['path']))) {
                        // $this->d('MATCH PARAMS ', $match);
                        $hasLayers = true;
                        $instance = $this->resolveLayer($params, $next, $match);
                        $next = $instance;
                    }
                }
                $this->setState(static::STATE_LOOP);

                return $hasLayers ? $next : null;

            default:
                return null;
        }
    }

    public function count()
    {
        return $this->stack->count();
    }

    protected function resolveLayer(array $params, LayerInterface $next, array $matchParams)
    {
        if (isset($matchParams['path']) && '' === $matchParams['path']) {
            $matchParams['path'] = '/';
        }

        if ($params['class']) {
            if (! class_exists($params['class'])) {
                throw new Error('The stack frame class does not exist: '.$params['class']);
            }
            $refl = new ReflectionClass($params['class']);
            $instance = $refl->newInstanceArgs([$next, $params['config'] + $matchParams]);
        } elseif ($params['instance']) {
            $instance = $params['instance'];
            $instance->configure($params['config'] + $matchParams);
            $instance->setNext($next);
        } else {
            throw new Error('The stack frame parameters are insuffient');
        }

        return $instance;
    }
}
