<?php

namespace Blog;

use Server\Module as Base;
use Server\LayerInterface;

class Module extends Base
{
    public function __construct(LayerInterface $next = null, array $config = [], array $env = [])
    {
        parent::__construct($next, $config, $env);


        $this->map(array(
            'pattern' => '/',
            'fn' => function ($req, $res) {
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
        ));

        $this->map(array(
            'pattern' => '*',
            'fn' => function ($req, $res) {
                return array(
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
        ));
    }
}
