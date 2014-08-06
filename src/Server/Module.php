<?php

namespace Server;

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
        return $this->next ? $this->next->call($req, $err) : parent::call($req, $err);
    }

    public function map(array $params)
    {
    	$this->routes[] = $params;
    }
}
