<?php namespace codesaur\HTML;

use Twig\TwigFilter;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigTemplate extends Template
{
    public function set(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }
    
    public function twig(string $template, array $vars = null)
    {
        (new TwigTemplate($template, $vars))->render();
    }
    
    protected function compile(string $html) : string
    {
        $loader = new ArrayLoader(array('result' => $html));
        $twig = new Environment($loader, array('autoescape' => false));
        
        $twig->addGlobal('app', \codesaur::app());
        $twig->addGlobal('user', \codesaur::user());
        $twig->addGlobal('request', \codesaur::request());
        $twig->addGlobal('language', \codesaur::language());
        $twig->addGlobal('controller', \codesaur::controller());
        
        $twig->addFilter(new TwigFilter('int', function($variable) { return \intval($variable); }));
        $twig->addFilter(new TwigFilter('text', function($string) { return \codesaur::text($string); }));
        $twig->addFilter(new TwigFilter('link', function($string, $params = []) { return \codesaur::link($string, $params); }));
        $twig->addFilter(new TwigFilter('json_decode', function($data, $param = true) { return \json_decode($data, $param); }));
        
        return $twig->render('result', $this->getVars());
    }
}
