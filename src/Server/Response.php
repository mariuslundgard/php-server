<?php

namespace Server;

class Response
{
    protected $req;
    protected $body;
    protected $headers;

    public function __construct(Request $req)
    {
        $this->req = $req;
        $this->body = '';
        $this->headers = [];
    }

    public function __get($property)
    {
        switch ($property) {
            case 'body':
                return $this->body;

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
