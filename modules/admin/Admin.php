<?php

class Admin extends Controller
{
    protected $object;

    public function beforeHandle($request)
    {
        Resources::add('static/admin.css');
        if (!Authentication::user()) Authentication::create($this)->challenge();
    }

    public function baseLink()
    {
        return $this->parent->link('panel', $this->getName()) . '/';
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

    public function index_action()
    {
        $this->redirect($this->link('list', reset(static::$managed_models)));
    }

    public function list_action($model)
    {
        if (!$model) $this->index_action();

        $me = $this;
        return array(
            'class' => $model,
            'collection' => View::create('collection')->render(array(
                'autocomplete' => View::create('autocomplete')->render(array(
                    'id' => $this->getName(),
                    'name' => $this->getName() . "[{$model}]",
                    'value' => 0,
                    'url' => $this->link('suggest', $model) . '/',
                    'link' => $this->link('edit', $model) . '/',
                    'label' => 'no ' . $model,
                    'required' => false,
                )),
                'allowCreate' => true,
                'class' => $model,
                'link' => function($id) use ($me, $model) { return $me->link('edit', $model, $id); },
                'values' => $model::get(),
            )),
        );
    }

    public function suggest_action($model, $hint)
    {
        $hint = strtolower(trim(strip_tags($hint)));
        $suggestions = array();

        if ($hint) foreach ($model::get() as $option) {
            if (strpos(strtolower($option->title()), $hint) !== false) $suggestions[] = array('value' => $option->id, 'label' => $option->title());
        }

        if (empty($suggestions)) {
            $suggestions[] = array('label' => 'no matches for ' . $hint);
        }

        return array('suggestions' => $suggestions);
    }

    public function edit_action($model, $id = null)
    {
        $this->object = $model::one($id) ?: $model::create();
        $fields = $this->object->getFields();
        $form = Form::create($model . 'Form', $fields, $this)->setTitle($this->object->title());

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
        $this->redirect($this->link('list', get_class($this->object)));
    }
}