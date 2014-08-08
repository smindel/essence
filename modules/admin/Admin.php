<?php

class Admin extends Controller
{
    protected $object;

    public function beforeHandle($request)
    {
        Resources::add('static/admin.css');
        if (Authentication::user()) return;
        Authentication::challenge();
    }

    public function baseLink()
    {
        return $this->parent->link('panel', $this->getName()) . '/';
    }

    public function getModel()
    {
        return is_array($this->consumed) && count($this->consumed) > 1 ? $this->consumed[1] : static::$managed_models[0];
    }

    public function getObject()
    {
        return $this->object;
    }

    public function Menu()
    {
        list(, , , , $currmodel) = explode('/', $this->getParent()->getRequest()->getUri());
        $items = array();
        if (count(static::$managed_models) > 1) foreach (static::$managed_models as $model) {
            $items[$model] = array(
                'Status' => strtolower($currmodel) == strtolower($model) ? 'section' : 'link',
                'Link' => $this->link('list', $model),
                'Title' => $model,
            );
        }
        return $items;
    }

    public function index_action() {
        $this->redirect($this->link('list', reset(static::$managed_models)));
    }

    public function list_action($model) {
        if (!$model) $this->index_action();

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
            'Links' => $links,
        );
    }

    public function edit_action($model, $id = null) {
        $this->object = $model::one($id) ?: $model::create();
        $fields = $this->object->getFields();
        $form = Form::create($model . 'Form', $fields, $this);

        return array(
            'Form' => $form->handleRequest($this->request),
        );
    }

    public function form_save(Form $form)
    {
        $this->object->hydrate($form->getData())->write();
        if ($this->request->getRaw($form->getName(), '_show_parent')) {
            $redirect = $this->link('list', get_class($this->object));
        } else {
            $redirect = $this->link('edit', get_class($this->object), $this->object->id);
        }
        $this->redirect($redirect);
    }

    public function form_delete(Form $form)
    {
        $this->object->delete();
        $this->redirect($this->link('index', get_class($this->object)));
    }
}