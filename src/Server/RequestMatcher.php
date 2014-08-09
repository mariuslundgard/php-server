<?php

namespace Server;

class RequestMatcher
{
    const WILDCARD_PREFIX = 'wildcard_';

    public static function matches(Request $req, array $params, $overridePath = null)
    {
        $params += array(
            'method' => '*',
            'pattern' => '*'
        );

        // test `method`
        if ('*' !== $params['method'] && $params['method'] !== $req->method) {
            return false;
        }

        $path = $overridePath ? $overridePath : $req->path;

        // test `pattern`
        if ('*' === $params['pattern']) {
            return compact('path');
        }

        $pattern = static::compileUriPattern($params['pattern']);

        // Note: only to avoid PHP CS error
        $matches = array();

        if (preg_match_all($pattern, $path, $matches)) {

            // return named parameters
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
