<?php

class RelationFormField extends FormField
{
    // ATTENTION: this is not the object of the parent form but the child form
    protected $object;

    public function setForm(Form $form)
    {
        parent::setForm($form);

        $options = array();
        $create = $join = $setnull = false;
        $name = $this->name;
        $object = $this->parent->getObject();
        if (($field = $object->getProperty($name, 'field'))) {
            list(, $action) = explode(':', $field . ':');
            $actions = explode('|', $action);
            list($metatype, $class, $option) = explode(':', $object->getProperty($name) . ':SET NULL');
            if ($metatype == 'LOOKUP') {
                if (in_array('add', $actions)) $create = $class;
                if (in_array('join', $actions)) $join = $class;
            }
            if ($metatype == 'FOREIGN') {
                if (in_array('add', $actions) && $option == 'SET NULL' || !$object->$name) $create = $class;
                if (in_array('join', $actions)) $join = $class;
                if ($option == 'SET NULL') $setnull = 'kein(e) ' . $class;
            }
        }
        $this->response = array(
            'Object' => $object,
            'Create' => $create,
            'Join' => $join,
            'SetNull' => $setnull,
            'Options' => $object->$name(),
            'Value' => $object->$name,
            'Remotefield' => $option,
        );
    }

    public function getObject()
    {
        return $this->object;
    }

    public function relationLink($id = false)
    {
        return $this->currentLink() . 'fields/' . $this->name . '/edit/' . $id;
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