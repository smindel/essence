<?php

// RelationFormField von Model abkoppeln
// HasOneFormField in ModelFormField und HasManyFormField in CollectionFormField umbenennen

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
        $remotejoinfield = $parentobject->getProperty($name, 'remotefield');
        foreach ($this->getOptions() as $id => $option) {
            if (!$option->$remotejoinfield || $option->$remotejoinfield->id != $parentobject->id) $remaining[$option->id] = $option;
        }
        return Collection::create($remaining);
    }

    public function getClass()
    {
        return $this->getParent()->getObject()->getProperty($this->name, 'remoteclass');
    }

    public function canSetNull()
    {
        return $this->getParent()->getObject()->getProperty($this->name, 'oninvalid') == 'SET NULL';
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
            switch ($this->parent->getObject()->getProperty($this->name, 'type')) {
                case 'LOOKUP':
                    $this->object = Base::create($this->parent->getObject()->getProperty($this->name, 'remoteclass'));
                    $remotefield = $this->parent->getObject()->getProperty($this->name, 'remotefield');
                    $this->object->$remotefield = $this->parent->getObject();
                    break;
                case 'FOREIGN':
                    $this->object = Base::create($this->parent->getObject()->getProperty($this->name, 'remoteclass'));
                    break;
                default:
                    throw new Exception(get_class($this) . ' can only be used on relations.');
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