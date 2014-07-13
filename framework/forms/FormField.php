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

    public function getFullName()
    {
        return $this->form->getName() . '[' . $this->name . ']';
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
        return str_replace('/', '_', $this->form->getAction() . '_' . $this->name);
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

    public function handleRequest($request)
    {
        aDebug(__CLASS__, __FUNCTION__, func_get_args());
    }

    public function currentLink()
    {
        $link = $this->form ? $this->form->currentLink() : BASE_URL;
        return $link . $this->getName() . '/' . implode('/', func_get_args());
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
        return '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"" . $this->getFullName() . "\" value=\"{$this->value}\" data-fyi-url=\"" . $this->currentLink() . "\"></div>";
    }
}