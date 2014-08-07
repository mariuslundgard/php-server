<?php

namespace Server;

class Request
{
    const WILDCARD_PREFIX = 'wildcard_';

    protected $method;
    protected $uri;
    protected $headers;

    public function __construct($method = 'GET', $uri = '/')
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = [];
    }

    public function __get($property)
    {
        switch ($property) {

            case 'method':
                return $this->method;

            case 'uri':
                return $this->uri;

            default:
                throw new Error('Nonexisting request property: '.$property);
        }
    }
}
