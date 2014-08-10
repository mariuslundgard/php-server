<?php

namespace Server;

class Request
{
    const WILDCARD_PREFIX = 'wildcard_';

    protected $method;
    protected $path;
    protected $query;
    protected $data;
    protected $headers;

    public function __construct($method = 'GET', $uri = '/', array $data = [], array $headers = [])
    {
        $this->method = $method;
        $this->setUri($uri);
        $this->data = $data;
        $this->headers = $headers;
    }

    public function __get($property)
    {
        switch ($property) {

            case 'method':
                return $this->method;

            case 'uri':
                return $this->path.(strlen($this->query) ? '?'.$this->query : '');

            case 'path':
                return $this->path;

            case 'query':
                return $this->query;

            case 'data':
                return $this->data;

            case 'headers':
                return $this->headers;

            default:
                throw new Error('Nonexisting request property: '.$property);
        }
    }

    public function setUri($uri)
    {
        $info = parse_url($uri) + array(
            'path' => '',
            'query' => ''
        );

        $this->path = $info['path'];
        $this->query = $info['query'];
    }

    public function isAjax()
    {
        return (null !== $this->headers['X-Requested-With'])
            && ('xmlhttprequest' === strtolower($this->headers['X-Requested-With']));
    }
}
