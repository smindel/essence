<?php

class RelationFormField extends FormField
{
    // ATTENTION: this is not the object of the parent form but the child form
    protected $object;

    public function setForm(Form $form)
    {
        parent::setForm($form);

        $options = array();
        $create = false;
        $name = $this->name;
        $object = $this->parent->getObject();
        if (($field = $object->getProperty($name, 'field'))) {
            list(, $action) = explode(':', $field . ':');
            $actions = explode('|', $action);
            list($metatype, $class, $remotefield) = explode(':', $object->getProperty($name));
            if ($metatype == 'LOOKUP' && in_array('add', $actions)) $create = $class;
        }

        $this->response = array(
            'Object' => $object,
            'Create' => $create,
            'Options' => $object->$name(),
            'Value' => $object->$name,
            'Remotefield' => $remotefield,
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

    public function edit_action($id)
    {
        if (($remoteoptions = $this->parent->getObject()->{$this->name}()) && isset($remoteoptions[$id])) {
            $this->object = $remoteoptions[$id];
        } else {
            list($type, $remoteclass, $remotefield) = explode(':', $this->parent->getObject()->getProperty($this->name));
            if ($type == 'LOOKUP') {
                $this->object = $remoteclass::create();
                $this->object->$remotefield = $this->parent->getObject();
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
        $form = Form::create($this->name . 'Form', $fields, $this);

        return array(
            'Form' => $form->handleRequest($this->request),
        );
    }

    public function form_save(Form $form)
    {
        $this->object->hydrate($form->getData())->write();
        $this->redirect($this->link('edit', $this->object->id));
    }

    public function form_delete(Form $form)
    {
        $this->object->delete();
        $this->redirect($this->parent->getParent()->currentLink());
    }
}