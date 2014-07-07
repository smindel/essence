<?php

class View extends Base
{
    protected $class;
    protected $template;

    public function __construct($class, $template)
    {
        $this->class = $class;
        $this->template = $template;
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
        $searchpaths = array('project', 'modules', 'framework');
        while (empty($template) && ($searchpath = array_shift($searchpaths))) {
            $pattern = "{$this->class}.{$this->template}.inc";
            $template = Finder::find($pattern, $searchpath);
        }
        if (!$template) throw new Exception("No template found for '{$this->class}:{$this->template}'");

        $cachedtemplate = 'cache' . DIRECTORY_SEPARATOR . str_replace(DIRECTORY_SEPARATOR, '.', $template);

        if (ENV_TYPE == 'dev' && isset($_REQUEST['flush']) || !file_exists($cachedtemplate)) {
            file_put_contents($cachedtemplate, preg_replace(array(
                '/\{\{\{/',
                '/\}\}\}/',
                '/\{\{/',
                '/\}\}/',
            ), array(
                '<?php echo ',
                '; ?>',
                '<?php ',
                ' ?>',
            ), file_get_contents($template)));
        }
        return $cachedtemplate;
    }
}