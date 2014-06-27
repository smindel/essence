<?php

class View extends Base
{
    public static function create()
    {
        if (func_num_args() != 2) throw new Exception("Invalid contructor parameters for '{$class}'");

        $controllerclass = func_get_arg(0);
        $methodname = func_get_arg(1);

        return parent::create(compact('controllerclass', 'methodname'));
    }

    public function render($data)
    {
        extract((array)$data);
        ob_start();
        require($this->template());
        return ob_get_clean();
    }

    public function template()
    {
        $template = strtolower("{$this->controllerclass}/{$this->methodname}.inc");
        if (ENV_TYPE == 'dev' || isset($_REQUEST['flush']) || !file_exists('cache/' . str_replace('/', '.', $template))) {
            file_put_contents('cache/' . str_replace('/', '.', $template), preg_replace(array(
                '/\{\{\{/',
                '/\}\}\}/',
                '/\{\{/',
                '/\}\}/',
            ), array(
                '<?php echo ',
                '; ?>',
                '<?php ',
                ' ?>',
            ), file_get_contents('templates/' . $template)));
        }
        return 'cache/' . str_replace('/', '.', $template);
    }
}