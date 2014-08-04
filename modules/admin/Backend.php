<?php

class Backend extends Controller
{
    public static $managers = array();

    public function beforeHandle($request)
    {
        Resources::add('static/backend.css');
        if (Authentication::user()) return;
        Authentication::challenge();
    }

    public function index_action()
    {
        $this->redirect($this->link('panel', reset(self::$managers)));
    }

    public function panel_action($panel = null)
    {
        if (!$panel) $this->index_action();

        $panel = $panel::create($this);

        return array(
            'Panel' => $panel->handleRequest($this->request),
        );
    }

    public function Menu()
    {
        $items = Collection::create();
        foreach (self::$managers as $managerclass) {
            $manager = $managerclass::create($this);
            $items[$managerclass] = array(
                'Title' => $manager->getName(),
                'Link' => $manager->link(),
                'Status' => strtolower($manager->getName()) == strtolower($this->getPanel()->getName()) ? 'section' : 'link',
                'Menu' => $manager->menu(),
            );
        }
        return $items->renderWithMe('backend.menu');
    }

    public function getPanel()
    {
        return Base::create($this->consumed[1], $this);
    }
}