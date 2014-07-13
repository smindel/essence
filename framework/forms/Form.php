<?php

class Form extends Controller
{
    protected $name;
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

        $this->parent = $controller;
        $this->method = $method;
        $this->fields = $fieldcollection;

        foreach ($fields as $field) $field->setForm($this);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function test_action($arg1, $arg2)
    {
        aDebug(__CLASS__, __FUNCTION__, func_get_args());
    }

    public function handleRequest($request)
    {
        self::$_curr = $this;
        $this->request = $request;

        if (method_exists($this, 'beforeHandle')) $this->beforeHandle($request);

        // does the current request carry a form submission
        // capture the form action selected
        $submitteddata = $request->getRaw($this->name) ?: array();
        $error = $callback = false;
        foreach ($this->fields as $field) {
            $submittedvalue = isset($submitteddata[$field->getName()]) ? $submitteddata[$field->getName()] : null;
            if ($field instanceof SubmitFormField && isset($submittedvalue)) {
                $callback = array($this->parent, $field->getName());
            }
        }

        // if this is a submission validate and set data on fields
        if ($callback) foreach ($this->fields as $field) {
            $submittedvalue = isset($submitteddata[$field->getName()]) ? $submitteddata[$field->getName()] : null;
            if (!$field->validate($submittedvalue)) {
                $field->setError('Validation failed');
                $error = true;
            }
            $field->setValue($submittedvalue);
        }

        // if there are unconsumed request segments handle or forward them
        $data = false;
        if ($this->request->peek() == $this->name) {
            list(, $segment) = $this->request->consume(2);
            if (method_exists($this, $segment . '_action')) {
                $data = $this->handleAction($segment);
            } else if (isset($this->fields[$segment])) {
                $data = $this->fields[$segment]->handleRequest($this->request);
            }
        }

        if (method_exists($this, 'afterRender')) $data = $this->afterRender($data);

        if ($error) {
            return $this->redirectBack();
        } else if ($callback) {
            return call_user_func($callback, $this);
        } else if ($data) {
            return $data;
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
        return $this->parent;
    }

    public function getObject()
    {
        return method_exists($this->getController(), 'getObject') ? $this->getController()->getObject() : false;
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
        $uri = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->parent->redirect($uri);
        die();
    }
}