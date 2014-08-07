<?php

namespace Server;

class Controller
{
    protected $app;
    protected $req;
    protected $res;

    public function __construct(Module $app, Request $req, Response $res)
    {
        $this->app = $app;
        $this->req = $req;
        $this->res = $res;
    }
}
