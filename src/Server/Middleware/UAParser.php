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
use UAParser\Parser;

/**
 * The user agent parsing middleware.
 *
 * @author Marius Lundgård <studio@mariuslundgard.com>
 */
class UAParser extends Layer
{
    public function call(Request $req, Error $err = null)
    {
        if ($err) {
            return parent::call($req, $err);
        }

        // parse the `User-Agent` header
        if ($header = $req->headers['User-Agent']) {

            // create an instance of the UA parser
            $parser = Parser::create();
            $result = $parser->parse($header);

            // set request properties
            $req->ua = $result->ua;
            $req->os = $result->os;
        }

        return parent::call($req);
    }
}
