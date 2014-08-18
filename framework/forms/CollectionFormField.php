<?php

class CollectionFormField extends ModelFormField
{
    protected $selected;

    public function __construct($name, $label = null, $selected = null, $options = null, $class = null)
    {
        parent::__construct($name, $label, null);
        $this->options = $options;
        $this->class = $class;
        $this->selected = $selected;
    }

    public function getCollectionControl()
    {
        $me = $this;
        return View::create('collection')->render(array(
            'class' => $this->getClass(),
            'link' => function($id) use ($me) { return $me->relationLink($id); },
            'values' => $this->getSelected(),
        ));
    }

    public function getSelected()
    {
        return $this->selected;
    }
}