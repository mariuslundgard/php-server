<?php

namespace Server;

class RequestMatcher
{
    const WILDCARD_PREFIX = 'wildcard_';

    public static function matches(Request $req, array $params)
    {
        $params += array(
            'method' => '*',
            'pattern' => '*'
        );

        // test `method`
        if ('*' !== $params['method'] && $params['method'] !== $req->method) {
            return false;
        }

        // d($params);

        // test `pattern`
        if ('*' === $params['pattern']) {
            return ['uri' => $req->uri];
        }

        $pattern = static::compileUriPattern($params['pattern']);

        // d($pattern, ' === ', $req->uri);

        // Note: only to avoid PHP CS error
        $matches = array();

        if (preg_match_all($pattern, $req->uri, $matches)) {

            // return named uri parameters
            $ret = [];
            foreach ($matches as $key => $val) {
                if (! is_numeric($key)) {
                    $ret[$key] = $val[0];
                }
            }

            return $ret;
        }

        return false;
    }

    protected static function compileUriPattern($pattern)
    {
        // Convert all characters to safe characters
        $pattern = preg_quote($pattern, '~');

        // -> /name/:key<regex>
        $pattern = preg_replace_callback(
            '/\\\:([A-Za-z0-9\_]+)\\\<([^\/]+)\>/',
            function ($match) {
                return '(?P<'.$match[1].'>'.stripslashes($match[2]).')';
            },
            $pattern
        );

        // -> /name/:key
        $pattern = preg_replace_callback(
            '/\\\:([A-Za-z0-9\_]+)/',
            function ($match) {
                return '(?P<'.$match[1].'>[^/]+)';
            },
            $pattern
        );

        // -> /name/*key
        $pattern = preg_replace_callback(
            '/\\\\\*([^\/]+)/',
            function ($match) {
                return '(?P<'.$match[1].'>.*)';
            },
            $pattern
        );

        // -> /name/*
        $pointer = 0;
        $pattern = preg_replace_callback(
            '/\\\\\*/',
            function ($match) use ($pointer) {
                return '(?P<'.static::WILDCARD_PREFIX.$pointer.'>.*)';
            },
            $pattern
        );

        // Add delimeters
        return '~^' . $pattern . '$~';
    }
}
