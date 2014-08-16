<?php

namespace Server;

class HttpParser
{
    /*
     * Parse lists of key-value pairs as described by RFC 2068 Section 2 and
     * convert them into an array
     */
    public static function parseDict($dict)
    {
        $ret = array();

        foreach (static::parseHttpList($dict) as $item) {
            if (-1 < strpos($item, '=')) {
                list($name, $value) = explode('=', $item);
                $ret[$name] = $value;
            } else {
                $ret[$item] = null;
            }
        }

        return $ret;
    }

    /*
     * Parse lists as described by RFC 2068 Section 2.
     */
    public static function parseHttpList($data)
    {
        $ret = array();
        $part = '';
        $escape = false;
        $quote = false;
        $index = 0;
        $size = strlen($data);

        while ($index < $size) {
            $char = $data[$index];

            switch (true) {
                case $escape:
                    $part .= $char;
                    $escape = false;
                    break;

                case $quote:
                    if ('\\' === $char) {
                        $escape = true;
                    } elseif ('"' === $char) {
                        $quote = false;
                    } else {
                        $part .= $char;
                    }
                    break;

                case ',' === $char:
                    array_push($ret, trim($part));
                    $part = '';
                    break;

                case '"' === $char:
                    $quote = true;
                    break;

                default:
                    $part .= $char;
                    break;
            }
            $index++;
        }

        if ($part) {
            array_push($ret, trim($part));
        }

        return $ret;
    }
}
