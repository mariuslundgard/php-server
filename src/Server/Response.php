<?php

namespace Server;

use Util\Dictionary;

class Response
{
    protected $status;
    protected $req;
    protected $body;
    protected $data;
    protected $headers;
    // protected $type;

    protected static $statusMessages = [

        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated, but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    ];

    public function __construct(Request $req)
    {
        $this->status = 200;
        $this->req = $req;
        $this->body = '';
        $this->data = new Dictionary();
        $this->headers = new Dictionary();
        // $this->type = 'text/html';
    }

    public function __get($property)
    {
        switch ($property) {

            // case 'body':
            //     return $this->body;

            // case 'headers':
            //     return $this->headers;

            // case 'data':
            //     return $this->data;

            case 'body':
                return $this->body;

            case 'length':
                return strlen($this->body);

            case 'status':
                return $this->status;

            case 'statusMessage':
                return $this->getStatusMessage();

            case 'headers':
                return $this->headers;

            case 'data':
                return $this->data;

            case 'type':
                return $this->headers->get('Content-Type', 'text/html');

            default:
                throw new Error('Nonexisting response property: '.$property);
        }
    }

    // public function __set($property, $value)
    // {
    //     switch ($property) {

    //         case 'body':
    //             $this->body = (string) $value;
    //             break;

    //         default:
    //             $this->data->set($property, $value);
    //             break;
    //     }
    // }

    public function __set($property, $value)
    {
        switch ($property) {

            case 'body':
                $this->body = (string) $value;
                break;

            // case 'type':
            //     $this->type = $value;
            //     break;

            case 'status':
                $this->setStatus($value);
                break;

            case 'type':
                $this->headers['Content-Type'] = $value;
                break;

            default:
                throw new Error('Nonexisting response property: '.$property);
        }
    }

    public function setStatus($code)
    {
        $code = intval($code);

        if (! isset(static::$statusMessages[$code])) {
            throw new Error('Not a valid HTTP response status code: '.$code);
        }

        if ($code !== $this->status) {
            $this->status = $code;
        }

        return $this;
    }

    public function write($str)
    {
        $this->body .= $str;

        return $this;
    }

    public function redirect($location)
    {
        $this->headers['Location'] = $this->req->uri($location);
        $this->send();
        exit;
    }

    /**
     * Get HTTP response status (compound of code and message).
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->status.' '.static::$statusMessages[$this->status];
    }

    public function send($appendToBody = '', $print = true)
    {
        $this->body .= $appendToBody;

        if ($print) {
            foreach ($this->headers->get() as $key => $value) {
                header($key.': '.$value);
            }

            if (200 !== $this->status) {
                header($this->getStatusHeader());

                if (304 === $this->status) {
                    return;
                }
            }

            echo $this->body;

            return;
        }

        if (304 === $this->status) {
            return '';
        }

        return $this->body;
    }

    public function getStatusHeader()
    {
        return $this->req->scheme . '/'.$this->req->version.' ' . $this->getStatusMessage();
    }
}
