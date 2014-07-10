<?php

abstract class Controller extends Base
{
    protected static $_curr;

    protected $request;

    public function handleRequest($request)
    {
        self::$_curr = $this;
        $this->request = $request;

        if (method_exists($this, 'beforeHandle')) $this->beforeHandle($request);

        $data = $this->handleAction($request->getMethodname(), $request->getParameters());

        if (method_exists($this, 'afterRender')) $data = $this->afterRender($data);

        echo $data;
    }

    public function handleAction($method, $parameters)
    {
        $data = call_user_func_array(array($this, $method . '_action'), $parameters);
        if (!is_string($data)) {
            $data = $this->render($method, $data);
        }
        return $data;
    }

    public function render($method, $data)
    {
        $output = View::create($this->getLayout($method))->render($data);
        if (!$this->getRequest()->isAjax() && ($layout = $this->getLayout())) {
            $data['_CONTENT_'] = $output;
            $output = View::create($layout)->render($data);
        }
        return $output;
    }

    public function getLayout($method = 'LAYOUT')
    {
        $i = 0;
        $class = get_class($this);
        $template = $class . '.' . $method;
        while (!($exists = View::exists($template)) && $class && $i < 5) {
            $class = get_parent_class($class);
            $template = $class . '.' . $method;
        }
        return $exists ? $template : false;
    }

    public function redirect($url, $code = 302)
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    public function redirectBack()
    {
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function link()
    {
        return BASE_URL . get_class($this) . '/' . implode('/', func_get_args());
    }

    public static function curr()
    {
        return self::$_curr;
    }

    public function getRequest()
    {
        return $this->request;
    }
}