<?php

class Admin extends Controller
{
    public static $managed_models = array();

    public function beforeHandle($request)
    {
        if (Authentication::user()) return;
        Authentication::challenge();
    }

    public function index_action() {
        $links = array();
        foreach (self::$managed_models as $model) {
            $links[$this->link('list', $model)] = $model;
        }
        return array(
            'Me' => $this,
            'Links' => $links,
        );
    }

    public function list_action() {
        $model = func_get_arg(0);
        $links = array(array(
            'link' => $this->link('edit', $model),
            'title' => "{$model} erstellen",
            'class' => 'create',
        ));
        foreach ($model::get() as $object) {
            $links[] = array(
                'link' => $this->link('edit', get_class($object), $object->id),
                'title' => $object->title(),
                'class' => 'edit',
            );
        }
        return array(
            'Model' => $model,
            'Me' => $this,
            'Links' => $links,
        );
    }

    public function edit_action($model, $id = null) {

        return array(
            'Me' => $this,
            'Form' => $this->form_action($model, $id),
        );
    }

    public function form_action($model, $id = null)
    {
        $object = $model::one((int)$id) ?: $model::create();
        $fields = $object->getFields();
        $form = Form::create($fields, $this, __FUNCTION__, $object)->setData($_REQUEST);
        // load data from object, then merge in data from previous submission
        return $form;
    }

    public function form_save(Form $form)
    {
        $object = $form->getObject();
        $object->hydrate($form->getData())->write();
        $this->redirect($this->link('edit', get_class($object), $object->id));
    }

    public function form_delete(Form $form)
    {
        $object = $form->getObject();
        $modelclass = get_class($object);
        $object->delete();
        $this->redirect($this->link('index', $modelclass));
    }
}