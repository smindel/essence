<?php

class TextFormField extends FormField
{
    protected $rows = 1;

    public function getRows()
    {
        return $this->rows < 1 ? 1 : (int)$this->rows;
    }

    public function setRows($rows)
    {
        $this->rows = (int)$rows;
        return $this;
    }

    public function html()
    {
        $html = '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label>";
        if ($this->getRows() == 1) {
            $html .= "<input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"" . $this->getFullName() . "\" value=\"{$this->value}\">";
        } else {
            $html .= "<textarea id=\"{$this->name}\" rows=\"" . $this->getRows() . "\" name=\"{$this->name}\">{$this->value}</textarea>";
        }
        $html .= '</div>';

        return $html;
    }
}