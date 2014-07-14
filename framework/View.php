<?php

class View extends Base
{
    protected $template;

    public function __construct($template)
    {
        if (empty($template)) throw new InvalidArgumentException("No template name specified in 'View->__construct()'");
        $this->template = $template;
    }

    public function render($data)
    {
        extract((array)$data);
        ob_start();
        require($this->compile());
        return ob_get_clean();
    }

    public static function exists($name)
    {
        return (bool)self::create($name)->findTemplate();
    }

    protected function findTemplate()
    {
        $template = false;
        $searchpaths = array('project', 'modules', 'framework');
        while (empty($template) && ($searchpath = array_shift($searchpaths))) {
            $pattern = "{$this->template}.inc";
            $template = Finder::find($pattern, $searchpath);
        }
        return $template;
    }

    protected function compile()
    {
        $template = $this->findTemplate();
        if (!$template) throw new Exception("No template found for '{$this->template}'");

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