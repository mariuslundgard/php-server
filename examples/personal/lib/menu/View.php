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

    public function __construct(Application $app, array $menuItems = array())
    {
        parent::__construct(__DIR__.'/index.php', dirname(__DIR__));

        $this->app = $app;
        $this->menuItems = array();

        $this->d('INITIAL MENU ITEMS', $menuItems);

        foreach ($menuItems as $item) {
            $this->addMenuItem($item);
        }
    }

    public function render(array $data = array(), $includePath = null)
    {
        // var_dump($this->menuItems);

        return parent::render(array(
            'app' => $this->app,
            'menuItems' => $this->menuItems
        ) + $data, $includePath);
    }

    public function addMenuItem($params)
    {
        // var_dump($params);

        $this->menuItems[] = $params + array(
            'uri' => '/',
            'label' => '[Menu Item Label]'
        );
    }
}
