<?php

namespace Server;

class HttpParser
{
    /*
     * Parse lists of key, value pairs as described by RFC 2068 Section 2 and
     * convert them into a python dict (or any other mapping object created from
     * the type with a dict like interface provided by the `cls` arugment):

     * >>> d = parse_dict_header('foo="is a fish", bar="as well"')
     * >>> type(d) is dict
     * True
     * >>> sorted(d.items())
     * [('bar', 'as well'), ('foo', 'is a fish')]

     * If there is no value for a key it will be `None`:

     * >>> parse_dict_header('key_without_value')
     * {'key_without_value': None}

     * To create a header from the :class:`dict` again, use the
     * :func:`dump_header` function.

     * :param value: a string with a dict header.
     * :param cls: callable to use for storage of parsed results.
     * :return: an instance of `cls`
     */
    public static function parseDict($header)
    {
        $ret = [];

        foreach (static::parseHttpList($header) as $item) {

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
     * In particular, parse comma-separated lists where the elements of
     * the list may include quoted-strings.  A quoted-string could
     * contain a comma.  A non-quoted string could have quotes in the
     * middle.  Neither commas nor quotes count if they are escaped.
     * Only double-quotes count, not single-quotes.
     */
    public static function parseHttpList($data)
    {
        $ret = [];
        $part = '';

        $escape = $quote = false;
        $index = 0;

        while ($index < strlen($data)) {
            $cur = $data[$index];

            if ($escape) {
                $part .= $cur;
                $escape = false;
                continue;
            } elseif ($quote) {
                if ('\\' === $cur) {
                    $escape = true;
                    continue;
                } elseif ('"' === $cur) {
                    $quote = false;
                }
                $part .= $cur;
                continue;
            } elseif (',' === $cur) {
                array_push($ret, $part);
                $part = '';
                continue;
            } elseif ('"' === $cur) {
                $quote = true;
            }

            $part .= $cur;

            $index++;
        }

        if ($part) {
            array_push($ret, $part);
        }

        foreach ($ret as $index => $p) {
            $ret[$index] = trim($p);
        }

        return $ret;
    }
}
