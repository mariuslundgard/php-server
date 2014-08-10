<?php

namespace Blog;

use Server\Controller as Base;

class Controller extends Base
{
    public function index()
    {
        return array(
            'view' => 'blog/index',
            'styleSheets' => [
                'http://localhost/~mariuslundgard/body/dist/body.css'
            ],
            'scripts' => [
                'http://localhost/~mariuslundgard/body/dist/body.js'
            ],
            'bodyClassNames' => [
                'no-margin'
            ]
        );
    }

    public function read($path)
    {
        return compact('path') + array(
            'view' => 'blog/read',
            'styleSheets' => [
                'http://localhost/~mariuslundgard/body/dist/body.css'
            ],
            'scripts' => [
                'http://localhost/~mariuslundgard/body/dist/body.js'
            ],
            'bodyClassNames' => [
                'no-margin'
            ]
        );
    }
}
