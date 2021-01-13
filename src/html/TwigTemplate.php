<?php namespace codesaur\HTML;

use Twig\Markup;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;

class TwigTemplate extends FileTemplate
{
    private $_twig;
    
    function __construct(string $template = null, array $vars = null)
    {
        parent::__construct($template, $vars);
        
        $this->_twig = new Environment(new ArrayLoader(), array('autoescape' => false));
        
        $this->addFilter('int', function($variable) { return \intval($variable); });
        $this->addFilter('json_decode', function($data, $param = true) { return \json_decode($data, $param); });
 
        $this->addFunction('script', function($src, $attr = 'defer') {
            return new Markup('<script ' . $attr . ' src="' . $src . '"></script>', 'UTF-8');
        });
        
        $this->addFunction('stylesheet', function($href, $attr = null) {
            return new Markup('<link href="' . $href . '" rel="stylesheet" type="text/css" ' . $attr . '>', 'UTF-8');
        });
    }
    
    public function &twig() : Environment
    {
        return $this->_twig;
    }
    
    public function addGlobal($name, $value)
    {
        $this->_twig->addGlobal($name, $value);
    }

    public function addFilter(string $name, $callable = null, array $options = [])
    {
        $this->_twig->addFilter(new TwigFilter($name, $callable, $options));
    }
        
    public function addFunction(string $name, $callable = null, array $options = [])
    {
        $this->_twig->addFunction(new TwigFunction($name, $callable, $options));
    }

    protected function compile(string $html) : string
    {
       $this->_twig->getLoader()->setTemplate('result', $html);
       return $this->_twig->render('result', $this->getVars());
    }
}
