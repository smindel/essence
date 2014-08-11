<?php

class HtmlFormField extends FormField
{
    protected $showHolder = false;

    public function setValue($value)
    {
        return $this;
    }

    public function setShowHolder($show)
    {
        $this->showHolder = $show;
        return $this;
    }

    public function getShowHolder()
    {
        return $this->showHolder;
    }
}