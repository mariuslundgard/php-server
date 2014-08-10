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
use Server\LayerInterface;
use Server\Request;
use Server\Response;
use Server\Error;

/**
 * The error handler.
 *
 * @author  Marius Lundgård <marius.lundgard@gmail.com>
 */
class ErrorHandler extends Layer
{
    public function __construct(LayerInterface $next = null, array $config = [])
    {
        parent::__construct($next, $config + [
            'view' => 'error',
        ]);
    }

    public function call(Request $req, Error $err = null)
    {
        // $this->d('call()', $req->dump());

        if (! $err) {

            // get application response
            try {

                //
                $res = parent::call($req);

                //
                if (! $res) {
                    throw new Error('No response');
                }

            } catch (Error $err) {
                // do nothing but get variable reference
            } catch (Exception $err) {
                // create studio http error object
                $err = new Error($err->getMessage());
            }
        }

        if ($err) {

            // create a fresh response
            $res = new Response($req);

            // echo $err->getMessage();

            // set response status code
            $res->status = $err->getCode() < 400 ? 500 : $err->getCode();

            // set error data
            $res->data->set(compact('err', 'req', 'res') + [
                'layout' => $this->config['view'],
                'view'   => $this->config['view'],
                'title'  => $err->getMessage(),
            ]);

            d($res->view);
            exit;
        }

        return $res;
    }
}
