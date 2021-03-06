<?php

class Collection extends Base implements Iterator, ArrayAccess, Serializable
{
    protected $data;

    public function __construct($data = array())
    {
        $this->setData($data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = (array)$data;
        return $this;
    }

    public function insert_before($before, $newval, $newkey = null)
    {
        $new = array();
        foreach ($this->data as $key => $val) {
            if ($key == $before) $new[$newkey] = $newval;
            $new[$key] = $val;
        }
        $this->data = $new;
    }

    public function insert_after($after, $newval, $newkey = null)
    {
        $new = array();
        foreach ($this->data as $key => $val) {
            $new[$key] = $val;
            if ($key == $after) $new[$newkey] = $newval;
        }
        $this->data = $new;
    }

    public function renderWith($template)
    {
        return View::create($template)->render($this->data);
    }

    public function renderWithMe($template)
    {
        return View::create($template)->render(array('Me' => $this->data));
    }

    public function current () { return current($this->data); }
    public function key () { return key($this->data); }
    public function next () { return next($this->data); }
    public function rewind () { return reset($this->data); }
    public function valid () { return $this->key(); }
    public function offsetExists ($offset) { return array_key_exists($offset, $this->data); }
    public function offsetGet ($offset) { return $this->data[$offset]; }
    public function offsetSet ($offset, $value) { $this->data[$offset] = $value; }
    public function offsetUnset ($offset) { unset($this->data[$offset]); }
    public function serialize () { return serialize($this->data); }
    public function unserialize ($serialized) { $this->data = unserialize($serialized); }

    public function count() { return count($this->data); }
    public function shift() { return array_shift($this->data); }
    public function insertBefore($before, $index, $value) {
        if (array_key_exists($index, $this->data)) unset($this->data[$index]);
        $data = array();
        foreach ($this->data as $key => $val) {
            if ($key == $before) $data[$index] = $value;
            $data[$key] = $val;
        }
        $this->data = $data;
    }
}