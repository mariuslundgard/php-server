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
        $res = parent::call($req, $err);

        if ($res->length) {
            return $res;
        }

        switch ($res->type) {

            case 'application/json':
                return $this->renderJson($res);

            case 'text/html':
                return $this->renderHtml($req, $res);

            case 'text/plain':
                return $res;

            default:
                throw new Error('Unsupported response type: '.$res->type);
        }
    }

    public function renderJson(Response $res)
    {
        $data = $res->data->get();

        unset($data['app'], $data['req'], $data['res'], $data['menu']);

        $res->body = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return $res;
    }

    public function renderHtml(Request $req, Response $res)
    {
        if (! $viewPath = $this->config['viewPath']) {
            throw new Error('Missing `viewPath` parameter for the `Server\Middleware\Renderer` middleware');
        }

        if (! file_exists($viewPath)) {
            throw new Error('The `viewPath` points to a nonexistent path');
        }

        $res->data->set(array('app' => $this->master) + compact('req', 'res'));

        $view = $res->data->get('view', $this->config['defaultView']);
        $layout = $res->data->get('layout', $this->config['defaultLayout']);

        if ($view) {
            $viewPathName = $this->config['viewPath'].'/'.$view.'.php';

            if (! file_exists($viewPathName)) {
                throw new Error('The view file does not exist: '.$viewPathName);
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
