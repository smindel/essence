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
}