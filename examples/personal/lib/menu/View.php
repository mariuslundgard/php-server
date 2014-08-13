<?php

namespace Menu;

use Server\View as Base;
use Application;
use Debug\DebuggableTrait;

class View extends Base
{
    use DebuggableTrait;

    protected $app;
    protected $menuItems;
    protected $actions;

    public function __construct(Application $app)
    {
        parent::__construct(__DIR__.'/index.php', dirname(__DIR__));

        $this->app = $app;
        $this->menuItems = array();
        $this->actions = array();
    }

    public function render(array $data = array(), $includePath = null)
    {
        return parent::render(array(
            'app' => $this->app,
            'menuItems' => $this->menuItems,
            'actions' => $this->actions
        ) + $data, $includePath);
    }

    public function addMenuItem($params)
    {
        $this->menuItems[] = $params + array(
            'uri' => '/',
            'label' => '[Menu Item Label]'
        );
    }

    public function addActionItem($params)
    {
        $this->actions[] = $params + array(
            'uri' => '/',
            'label' => '[Menu Item Label]'
        );
    }
}
