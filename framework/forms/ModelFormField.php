<?php

// ModelFormField von Model abkoppeln
// ObjectFormField in ModelFormField und CollectionFormField in CollectionFormField umbenennen

class ModelFormField extends FormField
{
    // ATTENTION: this is not the object of the parent form but the child form
    protected $options;
    protected $class;
    protected $hydrate = array();

    public function getAutocompleteControl()
    {
        return View::create('autocomplete')->render(array(
            'id' => $this->getName(),
            'name' => $this->getFullName(),
            'value' => $this->getValue() ?: 0,
            'url' => $this->currentlink() . 'fields/' . $this->getName() . '/suggest/',
            'link' => ($object = $this->parent->getObject()) && $object->id ? $this->relationLink() : null,
            'label' => $this->getValue() ? $this->getObject()->title() : 'no ' . $this->getClass(),
            'required' => $this->getRequired(),
        ));
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setHydrate($values)
    {
        $this->hydrate = $values;
        return $this;
    }

    public function relationLink($id = false)
    {
        return $this->currentLink() . 'fields/' . $this->name . '/edit/' . $id;
    }

    public function getForm($id)
    {
        if (isset($this->options[$id])) {
            $this->object = $this->options[$id];
        } else {
            $this->object = Base::create($this->getClass())->hydrate($this->hydrate);
        }

        $fields = $this->object->getFields();

        return Form::create($this->name . 'Form', $fields, $this)->setTitle($this->object->title());
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

    public function suggest_action($hint = '')
    {
        $hint = strtolower(trim(strip_tags($hint)));
        $suggestions = array();

        if (!$this->getRequired()) {
            $suggestions[] = array('value' => 0, 'label' => 'no ' . $this->getClass());
        }

        if ($hint) foreach ($this->options as $option) {
            if (strpos(strtolower($option->title()), $hint) !== false) $suggestions[] = array('value' => $option->id, 'label' => $option->title());
        }

        if (empty($suggestions)) {
            $suggestions[] = array('label' => 'no matches for ' . $hint);
        }

        return array('suggestions' => $suggestions);
    }
}