<?php

class ReadonlyFormField extends FormField
{
    // todo:
    // - readonlyfield can not create new record
    // - make form::handleRequest inherit from controller::handleRequest and use beforeHandle and afterRender

    public function __toString()
    {
        $name = $this->name;
        $object = $this->parent->getObject();
        if ($object->getProperty($name, 'field')) {
            list(, $action) = explode(':', $object->getProperty($name, 'field') . ':');
            $actions = explode('|', $action);
            list($metatype, $class, $remotefield) = explode(':', $object->getProperty($name));
        } else {
            $metatype = false;
        }
        if ($metatype == 'LOOKUP') {
            $values = in_array('add', $actions) ? array('<li class="create"><a href="' . $this->relationLink() . '">' . $class . ' erstellen</a></li>') : array();
            foreach ($object->$name as $option) {
                $values[] = '<li class="edit"><a href="' . $this->relationLink($option->id) . '">' . $option->title() . '</a></li>';
            }
            $value = "<ul id=\"{$this->name}\">" . implode($values) . '</ul>';
        } else {
            $value = '<div>' . $this->value . '</div>';
        }
        return '<div class="field ' . get_class($this) . "\"><label for=\"{$this->name}\">{$this->label}</label>{$value}</div>";
    }

    // ATTENTION: this is not the object of the parent form but the child form
    protected $object;

    public function getObject()
    {
        return $this->object;
    }

    public function relationLink($id = false)
    {
        return $this->currentLink() . $this->name . '/edit/' . $id;
    }

    public function edit_action($id)
    {
        $this->object = $this->parent->getObject()->{$this->name}()[$id];
        $fields = $this->object->getFields();
        $form = Form::create($this->name . 'Form', $fields, $this, __FUNCTION__);
        $form->setAction($this->link('edit', $id));
        // aDebug(
        //     $this->currentLink(),
        //     $this->link(),
        //     $this->parent->currentLink(),
        //     $this->consumed
        // );
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
        $this->redirect($this->link('index'));
    }
}