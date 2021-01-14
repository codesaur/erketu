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
        
        $this->_twig->addFilter(new TwigFilter('int', function($variable)
        {
            return \intval($variable);
        }));
        
        $this->_twig->addFilter(new TwigFilter('json_decode', function($data, $param = true)
        {
            return \json_decode($data, $param);
        }));
 
        $this->_twig->addFunction(new TwigFunction('script', function($src, $attr = 'defer')
        {
            $script = '<script';
            if ( ! empty($attr)) {
                $script .= " $attr";
            }
            $script .= ' src="' . $src . '"></script>';
            
            return new Markup($script, 'UTF-8');
        }));
        
        $this->_twig->addFunction(new TwigFunction('stylesheet', function($href, $attr = null)
        {
            $link = '<link href="' . $href . '" rel="stylesheet" type="text/css"';
            if ( ! empty($attr)) {
                $link .= " $attr";
            }
            $link .= '>';
            
            return new Markup($link, 'UTF-8');
        }));
    }
    
    public function &twig(): Environment
    {
        return $this->_twig;
    }

    protected function compile(string $html): string
    {
       $this->_twig->getLoader()->setTemplate('result', $html);
       return $this->_twig->render('result', $this->getVars());
    }
}
