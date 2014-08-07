<?php

class RelationFormField extends FormField
{
    // ATTENTION: this is not the object of the parent form but the child form
    protected $object;

    public function getObject()
    {
        if ($this->object) return $this->object;
        $name = $this->name;
        return $this->parent->getObject()->$name;
    }

    public function getOptions()
    {
        $name = $this->name;
        return $this->parent->getObject()->$name();
    }

    public function getRemainingOptions()
    {
        $name = $this->name;
        $remaining = array();
        $parentobject = $this->getParent()->getObject();
        list(,,$remotejoinfield) = explode(':', $parentobject->getProperty($name));
        foreach ($this->getOptions() as $id => $option) {
            if (!$option->$remotejoinfield || $option->$remotejoinfield->id != $parentobject->id) $remaining[$option->id] = $option;
        }
        return Collection::create($remaining);
    }

    public function canCreate()
    {
        $name = $this->name;
        $parentobject = $this->getParent()->getObject();
        if (($field = $parentobject->getProperty($name, 'field'))) {
            list(, $action) = explode(':', $field . ':');
            $actions = explode('|', $action);
            list($metatype, $class, $option) = explode(':', $parentobject->getProperty($this->name) . ':SET NULL');
            if ($metatype == 'LOOKUP' && in_array('add', $actions)) {
                return $class;
            } else if ($metatype == 'FOREIGN' && in_array('add', $actions) && $option == 'SET NULL' || !$parentobject->$name) {
                return $class;
            }
        }
        return false;
    }

    public function canJoin()
    {
        $name = $this->name;
        $parentobject = $this->getParent()->getObject();
        if (($field = $parentobject->getProperty($name, 'field'))) {
            list(, $action) = explode(':', $field . ':');
            $actions = explode('|', $action);
            list($metatype, $class, $option) = explode(':', $parentobject->getProperty($this->name) . ':SET NULL');
            if ($metatype == 'LOOKUP' && in_array('join', $actions)) {
                return $class;
            } else if ($metatype == 'FOREIGN' && in_array('join', $actions)) {
                return $class;
            }
        }
        return false;
    }

    public function canSetNull()
    {
        list($metatype, $class, $option) = explode(':', $this->getParent()->getObject()->getProperty($this->name) . ':SET NULL');
        return $metatype == 'FOREIGN' && $option == 'SET NULL' ? $class : false;
    }

    public function relationLink($id = false)
    {
        return $this->currentLink() . 'fields/' . $this->name . '/edit/' . $id;
    }

    public function index_action()
    {
        return 'sarah';
    }

    public function getForm($id)
    {
        if (($remoteoptions = $this->parent->getObject()->{$this->name}()) && isset($remoteoptions[$id])) {
            $this->object = $remoteoptions[$id];
        } else {
            list($type, $remoteclass, $remotefield) = explode(':', $this->parent->getObject()->getProperty($this->name) . ':SET NULL');
            if ($type == 'LOOKUP') {
                $this->object = $remoteclass::create();
                $this->object->$remotefield = $this->parent->getObject();
            } else if ($type == 'FOREIGN') {
                $this->object = $remoteclass::create();
            }
        }

        $breadcrumbs = array();
        $curr = $this;
        while ($curr) {
            if ($curr instanceof Form) array_unshift($breadcrumbs, "<a href=\"{$curr->link()}\">{$curr->getObject()->title()}</a>");
            $curr = $curr->getParent();
        }

        $fields = $this->object->getFields();
        $fields->insertBefore('Header', 'BreadCrumbs', HtmlFormField::create('BreadCrumbs', null, implode(' > ', $breadcrumbs)));
        return Form::create($this->name . 'Form', $fields, $this);
    }

    public function edit_action($id)
    {
        return array(
            'Form' => $this->getForm($id)->handleRequest($this->request),
        );
    }

    public function form_save(Form $form)
    {
        $this->object->hydrate($form->getData())->write();
        if ($this->request->getRaw($form->getName(), '_show_parent')) {
            $redirect = $this->getParent()->link();
        } else {
            $redirect = $this->link('edit', $this->object->id);
        }
        $this->redirect($redirect);
    }

    public function form_delete(Form $form)
    {
        $this->object->delete();
        $this->redirect($this->parent->getParent()->currentLink());
    }
}