<?php

class SubmitFormField extends FormField
{
    protected $callback;

    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->callback ?: array($this->parent->getParent(), $this->getName());
    }
}