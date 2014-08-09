<?php

namespace Server;

use Util\Dictionary;

class Response
{
    protected $req;
    protected $body;
    protected $headers;
    protected $data;
    protected $type;

    public function __construct(Request $req)
    {
        $this->req = $req;
        $this->body = '';
        $this->headers = [];
        $this->data = new Dictionary();
        $this->type = 'text/html';
    }

    public function __get($property)
    {
        switch ($property) {

            case 'body':
                return $this->body;

            case 'headers':
                return $this->headers;

            case 'data':
                return $this->data;

            case 'type':
                return $this->type;

            default:
                throw new Error('Nonexisting response property: '.$property);
        }
    }

    public function __set($property, $value)
    {
        switch ($property) {

            case 'body':
                $this->body = $value;
                break;

            case 'type':
                $this->type = $value;
                break;

            default:
                throw new Error('Nonexisting response property: '.$property);
        }
    }

    public function write($str)
    {
        $this->body .= $str;
        return $this;
    }

    public function send()
    {
        echo $this->body;
        return $this;
    }

    public function redirect($uri)
    {
        header('Location: '.$uri);
        return $this;
    }
}
