<?php

namespace Server;

use Util\Dictionary;

class Layer implements LayerInterface
{
    protected $next;
    protected $config;
    protected $app;

    public function __construct(LayerInterface $next = null, array $config = [])
    {
        $this->next = $next;
        $this->config = new Dictionary($config);
    }

    public function call(Request $req, Error $err = null)
    {
        if ($this->next) {
            return $this->next->call($req, $err);
        }

        return new Response($req);
    }

    public function setApp(LayerInterface $app)
    {
        $this->app = $app;
    }

    public function setNext(LayerInterface $next)
    {
        $this->next = $next;
    }
}
