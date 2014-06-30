<?php

class Admin extends Controller
{
    public function beforeHandle($request)
    {
        // $this->redirect('admin/login');
    }

    public function login_action() {}

    public function edit_action() {
        $model = func_get_arg(0);
        $id = func_num_args() > 1 ? func_get_arg(1) : 0;
        return array(
            'Form' => $this->form_action($model, $id),
        );
    }

    public function form_action($model, $id = null)
    {
        $object = $model::one((int)$id) ?: $model::create();
        $fields = $object->getFields();
        $form = Form::create($fields);
        // load data from object, then merge in data from previous submission
        return $form;
    }

    public function form_submit(Form $form, array $data)
    {
        $object = $form->object;
        $object->hydrate($data)->write();
        $this->redirect($this->link('edit', get_class($object), $object->id));
    }
}