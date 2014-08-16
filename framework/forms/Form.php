<?php

class Form extends Controller
{
    protected $name;
    protected $fields;
    protected $action;
    protected $messageText;
    protected $messageType;

    // alle formfield::tostring methods muessen gegen index_actions ersetzt werden,
    // entsprechende formfield.index.inc templates muessen erstellt werden

    public function __construct($name, $fields, $controller)
    {
        $this->name = $name;
        if (is_array($fields)) {
            $fieldcollection = Collection::create();
            foreach ($fields as $field) $fieldcollection[$field->getName()] = $field;
        } else if ($fields instanceof Collection){
            $fieldcollection = $fields;
        }

        $this->parent = $controller;
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

    public function getFieldsByFieldSet()
    {
        $fieldsets = array('_FORM_HEADER_' => array());
        foreach ($this->getFields() as $name => $field) {
            $fieldset = $field->getFieldSet() ?: '_FORM_FOOTER_';
            if (empty($fieldsets[$fieldset])) $fieldsets[$fieldset] = array();
            $fieldsets[$fieldset][] = $field;
        }
        return $fieldsets;
    }

    public function index_action()
    {
        // does the current request carry a form submission
        // capture the form action selected
        $submitteddata = $this->request->getRaw($this->name) ?: array();
        $error = $callback = false;
        foreach ($this->fields as $field) {
            $submittedvalue = array_key_exists($field->getName(), $submitteddata) ? $submitteddata[$field->getName()] : null;
            if ($field instanceof SubmitFormField && isset($submittedvalue)) {
                $callback = array($this->parent, $field->getName());
            }
        }

        // if this is a submission validate and set data on fields
        if ($callback) {
            foreach ($this->fields as $field) {
                $submittedvalue = array_key_exists($field->getName(), $submitteddata) ? $submitteddata[$field->getName()] : null;
                $error = $field->validate($submittedvalue) ? $error : true;
                $field->setValue($submittedvalue);
            }
            if ($error) {
                $this->setMessage('An error occurred', 'error');
            } else {
                return call_user_func($callback, $this);
            }
        }

        return array();
    }

    public function setMessage($msg, $type = 'info')
    {
        $this->messageText = $msg;
        $this->messageType = $type;
        return $this;
    }

    public function getMessage()
    {
        if (empty($this->messageText)) return false;
        return array('text' => $this->messageText, 'type' => $this->messageType);
    }

    public function fields_action($field)
    {
        return $this->fields[$field]->handleRequest($this->request);
    }

    public function getData()
    {
        $data = array();
        foreach ($this->fields as $field) {
            if ($field->getValue() !== null && $field->getName() !== null) $data[$field->getName()] = $field->getValue();
        }
        return $data;
    }

    public function getObject()
    {
        return method_exists($this->parent, 'getObject') ? $this->parent->getObject() : false;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getAction()
    {
        return $this->action ?: $this->request->getUri(true);
    }

    public function redirectBack()
    {
        $uri = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : parse_url($this->request->getUri(), PHP_URL_PATH);
        return $this->redirect($uri);
    }
}