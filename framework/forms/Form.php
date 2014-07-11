<?php

class Form extends Base
{
    protected $name;
    protected $controller;
    protected $method;
    protected $fields;
    protected $action;

    public function __construct($name, $fields, $controller, $method)
    {
        $this->name = $name;
        if (is_array($fields)) {
            $fieldcollection = Collection::create();
            foreach ($fields as $field) $fieldcollection[$field->getName()] = $field;
        } else if ($fields instanceof Collection){
            $fieldcollection = $fields;
        }

        $this->controller = $controller;
        $this->method = $method;
        $this->fields = $fieldcollection;

        foreach ($fields as $field) $field->setForm($this);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function handleSubmission($request)
    {
        $error = $callback = false;
        foreach ($this->fields as $field) {
            $submittedvalue = $request->getRaw($field->getName());;
            if ($field instanceof SubmitFormField && isset($submittedvalue)) {
                $callback = array($this->controller, $field->getName());
            }
        }

        if ($callback) foreach ($this->fields as $field) {
            $submittedvalue = $request->getRaw($field->getName());
            if (!$field->validate($submittedvalue)) {
                $field->setError('Validation failed');
                $error = true;
            }
            $field->setValue($submittedvalue);
        }

        if ($error) {
            return $this->redirectBack();
        } else if ($callback) {
            return call_user_func($callback, $this);
        } else {
            return $this;
        }
    }

    public function getData()
    {
        $data = array();
        foreach ($this->fields as $field) {
            if ($field->getValue() !== null && $field->getName() !== null) $data[$field->getName()] = $field->getValue();
        }
        return $data;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getObject()
    {
        return $this->getController()->getObject();
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getAction()
    {
        return Request::absolute_url($_SERVER['REQUEST_URI'], true);
    }

    public function html()
    {
        return Collection::create(array('Me' => $this))->renderWith('form');
    }

    public function redirectBack()
    {
        $this->controller->redirect($_SERVER['HTTP_REFERER']);
        die();
    }
}