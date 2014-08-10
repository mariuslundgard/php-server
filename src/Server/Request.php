<?php

namespace Server;

use Util\Dictionary;

class Request
{
    const WILDCARD_PREFIX = 'wildcard_';

    protected $scheme;
    protected $version;
    protected $method;
    protected $path;
    protected $query;
    protected $data;
    protected $headers;

    public function __construct($method = 'GET', $uri = '/', array $data = [], array $headers = [])
    {
        $this->scheme = 'HTTP';
        $this->version = '1.1';
        $this->method = $method;
        $this->setUri($uri);
        $this->data = new Dictionary($data);
        $this->headers = new Dictionary($headers);
    }

    public function __get($property)
    {
        switch ($property) {

            case 'scheme':
                return $this->scheme;

            case 'version':
                return $this->version;

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
        return (isset($this->headers['X-Requested-With']))
            && ('xmlhttprequest' === strtolower($this->headers['X-Requested-With']));
    }
}
