<?php

// ModelFormField von Model abkoppeln
// ObjectFormField in ModelFormField und CollectionFormField in CollectionFormField umbenennen

class ModelFormField extends FormField
{
    // ATTENTION: this is not the object of the parent form but the child form
    protected $options;
    protected $class;

    public function getOptions()
    {
        return $this->options;
    }

    public function getClass()
    {
        return $this->class;
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

        $fields = $this->object->getFields();
        $breadcrumbs = array();
        $curr = $this;
        while ($curr) {
            if ($curr instanceof Form) array_unshift($breadcrumbs, "<a href=\"{$curr->link()}\">{$curr->getObject()->title()}</a>");
            $curr = $curr->getParent();
        }
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