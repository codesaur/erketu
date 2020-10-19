<?php namespace codesaur\HTML;

use Twig\Markup;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;

class TemplateTwig extends Template
{
    protected $twig;
    
    function __construct(string $template = null, array $vars = null)
    {
        parent::__construct($template, $vars);
        
        $this->twig = new Environment(new ArrayLoader(), array('autoescape' => false));        
        $this->addFilter('int', function($variable) { return \intval($variable); });
        $this->addFilter('json_decode', function($data, $param = true) { return \json_decode($data, $param); });
        $this->addFunction('asset_script', function($src, $attr = 'defer') {
            return new Markup('<script ' . $attr . 'src="' . $src . '" type="text/javascript"></script>', 'UTF-8');
        });
        $this->addFunction('asset_stylesheet', function($href, $attr = null) {
            return new Markup('<link href="' . $href . '" rel="stylesheet" type="text/css" ' . $attr . '>', 'UTF-8');
        });
    }
    
    public function set(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }
    
    public function &getLoader() : LoaderInterface
    {
        return $this->twig->getLoader();
    }
    
    public function &getEnvironment() : Environment
    {
        return $this->twig;
    }
    
    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }

    public function addFilter(string $name, $callable = null, array $options = [])
    {
        $this->twig->addFilter(new TwigFilter($name, $callable, $options));
    }
        
    public function addFunction(string $name, $callable = null, array $options = [])
    {
        $this->twig->addFunction(new TwigFunction($name, $callable, $options));
    }

    protected function compile(string $html) : string
    {
        $this->twig->getLoader()->setTemplate('result', $html);
        return $this->twig->render('result', $this->getVars());
    }
}
