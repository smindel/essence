<?php

class NumberFormField extends FormField
{
    protected $min;
    protected $max;
    protected $step;

    public function getMin() { return $this->min; }
    public function setMin($min) { $this->min = $min; return $this; }
    public function getMax() { return $this->max; }
    public function setMax($max) { $this->max = $max; return $this; }
    public function getStep() { return $this->step; }
    public function setStep($step) { $this->step = $step; return $this; }
}