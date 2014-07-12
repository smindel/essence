<?php

class Admin extends Controller
{
    public static $managed_models = array();

    protected $object;

    public function beforeHandle($request)
    {
        Resources::add('static/admin.css');
        if (Authentication::user()) return;
        Authentication::challenge();
    }

    public function getObject()
    {
        return $this->object;
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

    public function list_action($model) {
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
        $this->object = $model::one($id) ?: $model::create();
        $fields = $this->object->getFields();
        $form = Form::create($model, $fields, $this, __FUNCTION__);
        $form->setAction($this->link('edit', $model, $id));

        return array(
            'Me' => $this,
            'Form' => $form->handleRequest($this->request),
        );
    }

    public function form_save(Form $form)
    {
        $this->object->hydrate($form->getData())->write();
        $this->redirect($this->link('edit', get_class($this->object), $this->object->id));
    }

    public function form_delete(Form $form)
    {
        $this->object->delete();
        $this->redirect($this->link('index', get_class($this->object)));
    }
}