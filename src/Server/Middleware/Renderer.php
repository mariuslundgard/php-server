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
use Server\Response;
use Server\Error;
use Server\View;

class Renderer extends Layer
{
    public function call(Request $req = null, Error $err = null)
    {
        if (! $this->config['viewPath']) {
            return $this->getNextResponse($req, new Error('Missing the `viewPath` parameter'));
        }

        if (! file_exists($this->config['viewPath'])) {
            return $this->getNextResponse($req, new Error('The view path does not exist: '.$this->config['viewPath']));
        }

        return $this->render($req, parent::call($req, $err));
    }

    public function render(Request $req, Response $res)
    {
        $res->data->set(array('app' => $this->master) + compact('req', 'res'));

        $view = $res->data->get('view', $this->config['defaultView']);
        $layout = $res->data->get('layout', $this->config['defaultLayout']);

        if ($view) {
            $viewPathName = $this->config['viewPath'].'/'.$view.'.php';
            if (! file_exists($viewPathName)) {
                return $this->getNextResponse($req, new Error('The view file does not exist: '.$viewPathName));
            }
            $view = new View($viewPathName);
            $res->body = $view->render($res->data->get(), $this->config['viewPath']);
        }

        if ($layout) {
            $res->data['view'] = $res->body;
            $layoutPathName = $this->config['layoutPath'].'/'.$layout.'.php';
            if (! file_exists($layoutPathName)) {
                throw new Error('The layout file does not exist: '.$layoutPathName);
            }
            $layout = new View($layoutPathName);
            $res->body = $layout->render($res->data->get(), $this->config['layoutPath']);
        }

        return $res;
    }

    // public function getPreferredType()
    // {
    //     $ret = array_keys($this->headers->get('Accept', [null => true]));

    //     return $ret[0];
    // }
}
