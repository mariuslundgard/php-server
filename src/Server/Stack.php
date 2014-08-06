<?php

namespace Server;

use SplStack;
use ReflectionClass;

class Stack extends Layer
{
    protected $env;
    protected $stack;

    public function __construct(LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config);

        $this->env = $env;
        $this->stack = new SplStack();
    }

    public function configure(array $config)
    {
        $this->config->merge($config);
    }

    public function call(Request $req = null, Error $err = null)
    {
        if (! $req) {
            $req = new Request();
        }

        return parent::call($req, $err);
    }

    public function employ(array $params)
    {
        $this->stack->push($params);
    }

    public function resolve(Request $req = null)
    {
        $next = $this;

        foreach ($this->stack as $params) {
            $params += array( 'pattern' => null, 'class' => null, 'instance' => null, 'config' => array() );

            if (! $params['pattern'] || RequestMatcher::matches($req, $params)) {

                if ($params['class']) {
                    if (! class_exists($params['class'])) {
                        throw new Error('The stack frame class does not exist');
                    }
                    // TODO: use Instantiator?
                    // $instantiator = new \Instantiator\Instantiator();
                    // $instance = $instantiator->instantiate('My\\ClassName\\Here');
                    $refl = new ReflectionClass($params['class']);
                    $instance = $refl->newInstanceArgs([$next, $params['config']]);
                } elseif ($params['instance']) {
                    $instance = $params['instance'];
                    $instance->setNext($next);
                    $instance->configure($params['config']);
                } else {
                    throw new Error('The stack frame parameters are insuffient');
                }

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
