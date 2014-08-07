<?php

namespace Server;

use Util\Dictionary;
use Debug\DebuggableTrait;

class Layer implements LayerInterface
{
    use DebuggableTrait;

    protected $next;
    protected $config;
    protected $app;

    public function __construct(LayerInterface $next = null, array $config = [])
    {
        $this->next = $next;
        $this->config = new Dictionary($config);
    }

    public function configure(array $config)
    {
        $this->config->merge($config);

        return $this;
    }

    public function call(Request $req, Error $err = null)
    {
        return $this->next ? $this->next->call($req, $err) : new Response($req);
    }

    public function setApp(LayerInterface $app)
    {
        $this->app = $app;
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
