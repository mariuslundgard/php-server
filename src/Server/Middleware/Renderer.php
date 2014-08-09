<?php

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
            throw new Error('Missing the `viewPath` parameter');
        }

        if (! file_exists($this->config['viewPath'])) {
            throw new Error('The view path does not exist: '.$this->config['viewPath']);
        }

        return $this->render(parent::call($req, $err));
    }

    public function render(Response $res)
    {
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
}
