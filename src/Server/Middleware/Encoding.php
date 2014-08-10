<?php

/*
 * This file is part of the Studio framework package for PHP.
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

class Encoding extends Layer
{
    public function call(Request $req, Error $err = null)
    {
        if ($err) {
            return parent::call($req, $err);
        }

        switch ($this->getPreferredEncoding($req)) {

            case 'gzip':
                ob_start('ob_gzhandler') || ob_start();
                break;
        }

        return parent::call($req);
    }

    // public function acceptsEncoding($encoding)
    // {
    //     return isset($this->headers['Accept-Encoding'][$encoding]);
    // }

    public function getPreferredEncoding(Request $req)
    {
        $header = $req->headers['Accept-Language'];

        if (! is_array($header)) {
            throw new Error(
                'The `Encoding` middleware requires the `AcceptParser` middleware '.
                'in order to use the `getPreferredEncoding()` method'
            );
        }

        $encodings = array_keys($header);

        return array_shift($encodings);
    }
}
