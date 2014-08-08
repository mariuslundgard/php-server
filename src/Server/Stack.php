<?php

namespace Server;

use SplStack;
use ReflectionClass;

class Stack extends Layer
{
    const STATE_IDLE = 0;
    const STATE_LOOP = 1;
    const STATE_DONE = 2;

    protected $env;
    protected $stack;
    // protected $isResolved;
    protected $state;

    public function __construct(LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config);

        $this->env = $env;
        $this->stack = new SplStack();
        // $this->isResolved = false;
        $this->state = static::STATE_IDLE;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function call(Request $req = null, Error $err = null)
    {
        if (! $req) {
            $req = new Request();
        }

        if ($app = $this->resolve($req)) {
            return $app->call($req, $err);
        }

        return $this->next ? $this->next->call($req, $err) : parent::call($req, $err);
    }

    public function employ(array $params)
    {
        $this->stack->push($params);

        return $this;
    }

    public function resolve(Request $req = null)
    {
        if (static::STATE_IDLE !== $this->state) {
        // if ($this->isResolved) {
            return null;
        }

        $this->setState(static::STATE_LOOP);
        // $this->isResolved = true;

        $next = $this;

        foreach ($this->stack as $params) {
            $params += array( 'pattern' => null, 'class' => null, 'instance' => null, 'config' => array() );
            $matchParams = array();

            if (! $params['pattern'] || $matchParams = RequestMatcher::matches($req, $params)) {

                if ($params['class']) {
                    if (! class_exists($params['class'])) {
                        throw new Error('The stack frame class does not exist: '.$params['class']);
                    }
                    // TODO: use Instantiator?
                    // $instantiator = new \Instantiator\Instantiator();
                    // $instance = $instantiator->instantiate('My\\ClassName\\Here');
                    $refl = new ReflectionClass($params['class']);
                    $instance = $refl->newInstanceArgs([$next, $params['config']]);
                } elseif ($params['instance']) {
                    $instance = $params['instance'];
                    $instance->setNext($next);
                } else {
                    throw new Error('The stack frame parameters are insuffient');
                }

                $instance->configure($matchParams + $params['config']);
                $next = $instance;
            }
        }

        return $next;
    }

    public function count()
    {
        return $this->stack->count();
    }
}
