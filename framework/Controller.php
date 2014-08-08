<?php

abstract class Controller extends Base
{
    protected static $_curr;

    protected $request;
    protected $parent;
    protected $method = 'index';
    protected $response;
    protected $consumed = array();
    protected $redirect;

    public function __construct(Controller $parent = null)
    {
        $this->parent = $parent;
    }

    public function handleRequest($request)
    {
        self::$_curr = $this;
        $this->request = $request;

        $this->method = $this->consume() ?: $this->method;

        if (method_exists($this, 'beforeHandle')) $this->beforeHandle($request);

        $this->response = $this->handleAction($this->method ?: 'index') ?: array();

        if (method_exists($this, 'beforeRender')) $this->beforeRender();

        return $this->__toString();
    }

    public function consume($numsegments = false)
    {
        $segments = $this->request->consume($numsegments);
        if ($numsegments === false) {
            $this->consumed[] = $segments;
        } else {
            foreach ($segments as $segment) $this->consumed[] = $segment;
        }
        return $segments;
    }

    public function handleAction($method)
    {
        $reflectionmethod = new ReflectionMethod($this, $method . '_action');
        $params = $this->consume(count($reflectionmethod->getParameters()));

        return $reflectionmethod->invokeArgs($this, $params);
    }

    public function getLayout($method = 'LAYOUT')
    {
        $i = 0;
        $class = get_class($this);
        $template = $class;
        if ($method) $template .= '.' . $method;
        while (!($exists = View::exists($template)) && $class && $i < 10) {
            $class = get_parent_class($class);
            $template = $class;
            if ($method) $template .= '.' . $method;
        }
        return $exists ? $template : false;
    }

    public function __toString()
    {
        try {
            if ($this->redirect) {
                return $this->parent ? '' : Request::create(Request::relative_url($this->redirect))->handle();
            }

            if (is_string($this->response)) return $this->response;

            $this->response['Me'] = $this;
            $layout = $this->getLayout($this->method);
            $output = View::create($layout)->render($this->response);
            if (!$this->parent && !$this->getRequest()->isAjax() && ($layout = $this->getLayout())) {
                $this->response['_CONTENT_'] = $output;
                $output = View::create($layout)->render($this->response);
            }
            return $output;
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    public function getName()
    {
        return get_class($this);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function redirect($url, $code = 302)
    {
        $this->redirect = $url;
        if (PHP_SAPI == 'cli') {
            if ($this->parent) $this->parent->redirect($url, $code);
        } else {
            header('Location: ' . $url, true, $code);
            exit;
        }
    }

    public function redirectBack()
    {
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function baseLink()
    {
        return rtrim($this->parent ? $this->parent->currentLink() : BASE_URL . $this->getName(), '/') . '/';
    }

    public function link()
    {
        $segments = func_get_args();
        if (count($segments) == 1 && is_array($segments[0])) {
            $segments = $segments[0];
        }

        return $this->baseLink() . implode('/', $segments);
    }

    public function currentLink()
    {
        return $this->link($this->consumed);
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