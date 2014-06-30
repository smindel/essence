<?php

class View extends Base
{
    public static function create()
    {
        if (func_num_args() != 2) throw new Exception("Invalid contructor parameters for '{$class}'");

        $class = func_get_arg(0);
        $template = func_get_arg(1);

        return parent::create(compact('class', 'template'));
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
        $template = strtolower("{$this->class}/{$this->template}.inc");
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