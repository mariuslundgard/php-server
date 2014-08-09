<?php

namespace Server;

class Request
{
    const WILDCARD_PREFIX = 'wildcard_';

    protected $method;
    protected $uri;
    protected $data;
    protected $headers;

    public function __construct($method = 'GET', $uri = '/', array $data = [], array $headers = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->data = $data;
        $this->headers = $headers;
    }

    public function __get($property)
    {
        switch ($property) {

            case 'method':
                return $this->method;

            case 'uri':
                return $this->uri;

            case 'data':
                return $this->data;

            case 'headers':
                return $this->headers;

            default:
                throw new Error('Nonexisting request property: '.$property);
        }
    }
}
