<?php

abstract class FormField extends Base
{
    protected $name;
    protected $label;
    protected $value;
    protected $form;

    public function __construct($name, $label = null, $value = null) {
        $this->name = $name;
        $this->label = $label ?: $name;
        $this->value = $value;
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

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getId()
    {
        return str_replace('/', '_', $this->form->action() . '_' . $this->name);
    }

    public function getHtmlType()
    {
        $guess = strtolower(substr(get_class($this), 0, -9));
        $htmltypes = array(
            'date',
            'datetime',
            'email',
            'hidden',
            'password',
            'submit',
            'text',
            'url',
        );
        return in_array($guess, $htmltypes) ? $guess : false;
    }

    public function validate($value)
    {
        return isset($this->validator) ? call_user_func($this->validator, $value, $this, $form) : true;
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

    public function html()
    {
        return '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\"></div>";
    }
}