<?php

/*
 * This file is part of the Server framework package for PHP.
 *
 * (c) Marius Lundgård <studio@mariuslundgard.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Server\Middleware;

use Server\Layer;
use ArrayAccess;
use Server\LayerInterface;
use Server\Request;
use Server\Error;

class Cookie extends Layer implements ArrayAccess
{
    protected $data;

    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config + [
            'path' => '/',
            'domain' => null,
            'defaultExpire' => 0,
            'secure' => false,
            'httponly' => false,
        ], $env);

        foreach ($_COOKIE as $name => $value) {
            $this->data[$name] = $value;
        }
    }

    public function call(Request $req, Error $err = null)
    {
        $req->cookie = $this;

        $res = parent::call($req, $err);

        return $res;
    }

    public function get($name = null, $default = null)
    {
        if (null === $name) {
            return $this->data;
        }

        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    public function set($name, $value, $expire = null)
    {
        $this->data[$name] = $value;

        $expire = null === $expire ? $this->config['defaultExpire'] : $expire;

        setcookie(
            $name,
            $value,
            $expire ? time() + $expire : null,
            $this->config['path'],
            $this->config['domain'],
            $this->config['secure'],
            $this->config['httponly']
        );
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        if (isset($_COOKIE[$offset])) {
            unset($_COOKIE[$offset]);
            setcookie(
                $offset,
                null,
                -1,
                $this->config['path'],
                $this->config['domain'],
                $this->config['secure'],
                $this->config['httponly']
            );
        }

        if (isset($this->data[$offset])) {
            unset($this->data[$offset]);
        }
    }
}
