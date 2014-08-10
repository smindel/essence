<?php

abstract class FormField extends Controller
{
    protected $name;
    protected $label;
    protected $value;
    protected $request;
    protected $method = false;
    protected $validator;
    protected $required = false;
    protected $check = false;

    public function __construct($name, $label = null, $value = null) {
        $this->name = $name;
        $this->label = $label ?: $name;
        $this->value = $value;
    }

    public function getFullName()
    {
        return $this->parent->getName() . '[' . $this->name . ']';
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function setForm(Controller $form)
    {
        $this->parent = $form;
        return $this;
    }

    public function getId()
    {
        return $this->parent->getName() . '_' . $this->getName();
    }

    public function getHtmlType()
    {
        $guess = strtolower(substr(get_class($this), 0, -9));
        $htmltypes = array(
            'date',
            'datetime',
            'email',
            'hidden',
            'number',
            'password',
            'submit',
            'text',
            'url',
        );
        return in_array($guess, $htmltypes) ? $guess : false;
    }

    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    public function setCheck($check)
    {
        $this->check = $check;
        return $this;
    }

    public function validate($value)
    {
        if ($this->validator) return call_user_func($this->validator, $value, $this, $form);
        if (!empty($value) && !empty($this->check) && !preg_match($this->check, $value)) return false;
        if (empty($value) && $this->required) return false;
        return true;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function setError($msg)
    {
        $_SESSION[$this->getId() . '_error'] = $msg;
    }

    public function getError($keep = false)
    {
        $msg = false;
        if (isset($_SESSION[$this->getId() . '_error'])) {
            $msg = $_SESSION[$this->getId() . '_error'];
            if (!$keep) unset($_SESSION[$this->getId() . '_error']);
        }
        return $msg;
    }
}