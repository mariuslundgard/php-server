<?php

namespace Server;

use Util\Dictionary;

class Response
{
    protected $req;
    protected $body;
    protected $headers;
    protected $data;

    public function __construct(Request $req)
    {
        $this->req = $req;
        $this->body = '';
        $this->headers = [];
        $this->data = new Dictionary();
    }

    public function __get($property)
    {
        switch ($property) {

            case 'body':
                return $this->body;

            case 'data':
                return $this->data;

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

            default:
                throw new Error('Nonexisting response property: '.$property);
        }
    }

    public function write($str)
    {
        $this->body .= $str;
    }

    public function send()
    {
        echo $this->body;
    }
}
