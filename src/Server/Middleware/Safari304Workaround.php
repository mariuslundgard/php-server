<?php

/*
 * This file is part of the Server framework package for PHP.
 *
 * (c) Marius Lundgård <studio@mariuslundgard.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Server\Middleware;

use Server\Layer;
use Server\Request;
use Server\Error;

/**
 * A workaround middleware for the `304 Not Modified` HTTP status in Safari
 *
 * ---> Really, Apple? In 2014?
 *
 * @author  Marius Lundgård <studio@mariuslundgard.com>
 */
class Safari304Workaround extends Layer
{
    public function call(Request $req, Error $err = null)
    {
        if (! property_exists($req, 'ua')) {
            throw new Error('The `Safari304Ignore` middleware requires previous employment of the `UAParser` middleware');
        }

        $res = parent::call($req, $err);

        if ('Safari' === $req->ua->family && 304 === $res->status) {
            d('IGNORE SAFARI 304');
            $res->status = 200;
        }

        return $res;
    }
}
