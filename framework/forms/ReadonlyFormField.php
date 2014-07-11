<?php

class ReadonlyFormField extends FormField
{
    public function html()
    {
        $name = $this->name;
        $object = $this->form->getObject();
        if (isset($object->db('field')[$name])) {
            list(, $action) = explode(':', $object->db('field')[$name] . ':');
            $actions = explode('|', $action);
            list($metatype, $class, $remotefield) = explode(':', $object->db('type')[$name]);
        } else {
            $metatype = false;
        }
        if ($metatype == 'LOOKUP') {
            $values = in_array('add', $actions) ? array('<li class="create"><a href="' . $class::create()->link() . '">' . $class . ' erstellen</a></li>') : array();
            foreach ($object->$name as $option) {
                $values[] = '<li class="edit"><a href="' . $option->link() . '">' . $option->title() . '</a></li>';
            }
            $value = "<ul id=\"{$this->name}\">" . implode($values) . '</ul>';
        } else {
            $value = '<div>' . $this->value . '</div>';
        }
        return '<div class="field ' . get_class($this) . "\"><label for=\"{$this->name}\">{$this->label}</label>{$value}</div>";
    }
}