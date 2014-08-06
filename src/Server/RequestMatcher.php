<?php

namespace Server;

class RequestMatcher
{
    const WILDCARD_PREFIX = 'wildcard_';

    public static function matches(Request $req, array $params)
    {
    	$params += array(
    		'method' => '*',
    		'pattern' => '*',
    	);

    	// test `method`
    	if ('*' !== $params['method'] && $params['method'] !== $req->method) {
    		return false;
    	}

    	// test `pattern`
    	if ('*' === $params['pattern']) {
    		return ['uri' => $req->uri];
    	}

        $pattern = static::_compileUriPattern($params['pattern']);

        if (preg_match_all($pattern, $req->uri, $matches)) {
        	// print_r($matches);
            // TODO: return named matches
            return true;
        }

        return false;
    }

    protected static function _compileUriPattern($pattern)
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
