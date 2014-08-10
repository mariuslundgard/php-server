<?php

/*
 * This file is part of the Server framework package for PHP.
 *
 * (c) Marius Lundgård <marius.lundgard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Server\Middleware;

use Server\Layer;
use Server\Request;
use Server\Error;

/**
 * The HTTP Accept layer.
 *
 * @author  Marius Lundgård <studio@mariuslundgard.com>
 */
class Accept extends Layer
{
    public function call(Request $req, Error $err = null)
    {
        if ($err) {
            return $this->next->call($req, $err);
        }

        if ($header = $req->headers['Accept']) {
            $req->headers['Accept'] = static::parse($header);
        }

        if ($header = $req->headers['Accept-Encoding']) {
            $req->headers['Accept-Encoding'] = static::parse($header);
        }

        if ($header = $req->headers['Accept-Language']) {
            $req->headers['Accept-Language'] = static::parse($header);
        }

        return $this->next->call($req);
    }

    /**
     * Parses the value of an Accept-style request header into a hash of
     * acceptable values and their respective quality factors (qvalues).
     */
    public static function parse($header)
    {
        $qValues = [];
        $items = str_trim_split($header, ',');

        foreach ($items as $item) {
            preg_match('/^([^\s,]+?)(?:\s*;\s*q\s*=\s*(\d+(?:\.\d+)?))?$/', $item, $matches);

            if ($matches) {
                $name = strtolower($matches[1]);
                $qValue = static::normalizeQValue(floatval(isset($matches[2]) ? $matches[2] : 1));
                $qValues[$name] = $qValue;
            } else {
                throw new Exception('Invalid header value: ' . json_encode($item, true));
            }
        }

        return $qValues;
    }

    /**
     * Converts 1.0 and 0.0 qvalues to 1 and 0 respectively. Used to maintain
     * consistency across qvalue methods.
     */
    public static function normalizeQValue($qValue)
    {
        return (($qValue === 1 || $qValue === 0) && is_numeric($qValue)) ? intval($qValue) : $qValue;
    }
}
