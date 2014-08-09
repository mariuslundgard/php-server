<?php

namespace Server;

interface LayerInterface
{
    public function call(Request $req, Error $err);
}
