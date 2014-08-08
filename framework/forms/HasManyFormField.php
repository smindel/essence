<?php

class HasManyFormField extends RelationFormField
{
    protected $selected;

    public function __construct($name, $label = null, $selected = null, $options = null, $class = null)
    {
        parent::__construct($name, $label, null);
        $this->options = $options;
        $this->class = $class;
        $this->selected = $selected;
    }

    public function getRemainingOptions()
    {
        $name = $this->name;
        $remaining = array();
        $parentobject = $this->getParent()->getObject();
        $remotejoinfield = $parentobject->getProperty($name, 'remotefield');
        foreach ($this->getOptions() as $id => $option) {
            if (!$option->$remotejoinfield || $option->$remotejoinfield->id != $parentobject->id) $remaining[$option->id] = $option;
        }
        return Collection::create($remaining);
    }
}