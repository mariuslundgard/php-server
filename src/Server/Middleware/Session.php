<?php

/*
 * This file is part of the Server framework package for PHP.
 *
 * (c) Marius Lundgård <marius.lundgard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Server\Middleware;

use Server\Layer;
use Server\LayerInterface;
use ArrayAccess;
use Server\Request;
use Server\Error;

class Session extends Layer implements ArrayAccess
{
    protected $isStarted;
    protected $flashData;

    public function __construct(LayerInterface $next = null, array $config = array(), array $env = array())
    {
        parent::__construct($next, $config + [
            'id' => '__PHPSERVER__',
            'delim' => '.',
        ], $env);

        $this->flashData = [];
        $this->start();
    }

    public function call(Request $req, Error $err = null)
    {
        // attach a reference to the instance to the request
        $req->session = $this;

        if ($err) {
            return parent::call($req, $err);
        }

        return parent::call($req);
    }

    public function start()
    {
        if ($this->isStarted) {
            return;
        }

        $this->isStarted = true;

        if (!isset($_SESSION)) {
            @session_start();
        }

        if ($id = $this->config['id']) {
            if (!isset($_SESSION[$id])) {
                $_SESSION[$id] = [];
            }
        }

        if (isset($_SESSION['__STUDIO_FLASH__'])) {
            $flashKeys = $_SESSION['__STUDIO_FLASH__'];
            foreach ($flashKeys as $key) {
                $this->flashData[$key] = $this->get($key);
                $this->remove($key);
            }
            unset($_SESSION['__STUDIO_FLASH__']);
        }
    }

    public function offsetExists($offset)
    {
        return ($id = $this->config['id'])
            ? delim_isset($_SESSION[$id], $offset, $this->config['delim'])
            : delim_isset($_SESSION, $offset, $this->config['delim']);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function get($key = null, $default = null)
    {
        // $this->start();

        $id = $this->config['id'];

        if (null === $key) {
            return ($id ? $_SESSION[$id] : $_SESSION) + $this->flashData;
        }

        if (isset($this->flashData[$key])) {
            return $this->flashData[$key];
        }

        $value = delim_get($id ? $_SESSION[$id] : $_SESSION, $key, $this->config['delim']);

        return $value ? $value : $default;
    }

    public function set($key, $value)
    {
        return ($id = $this->config['id'])
            ? delim_set($_SESSION[$id], $key, $value, $this->config['delim'])
            : delim_set($_SESSION, $key, $value, $this->config['delim']);
    }

    public function remove($key)
    {
        return ($id = $this->config['id'])
            ? delim_unset($_SESSION[$id], $key, $this->config['delim'])
            : delim_unset($_SESSION, $key, $this->config['delim']);
    }

    public function flash($key, $value)
    {
        if (empty($_SESSION['__STUDIO_FLASH__'])) {
            $_SESSION['__STUDIO_FLASH__'] = [];
        }

        $_SESSION['__STUDIO_FLASH__'] = array_merge([$key], $_SESSION['__STUDIO_FLASH__']);

        $this->set($key, $value);
    }

    public function clear()
    {
        if ($id = $this->config['id']) {
            $_SESSION[$id] = [];
        } else {
            $_SESSION = [];
        }
    }
}
