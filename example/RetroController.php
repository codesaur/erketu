<?php namespace My\Test\App;

use codesaur as single;
use codesaur\Http\Controller;
use codesaur\HTML\TwigTemplate;

class RetroController extends Controller
{
    public function index()
    {
        $this->getTemplate()->render();
    }
    
    public function hello(string $firstname, ?string $lastname = null)
    {
        $template = $this->getTemplate();
        
        if (empty($lastname)) {
            $template->set('user', $firstname);
        } else {
            $template->set('user', "$firstname $lastname");
        }
        
        $template->render();
    }
    
    function getTemplate() : TwigTemplate
    {
        // credits to template
        // Author: Robin Selmer
        // August 22, 2017
        // RETRO PAGE - Hacker themed page
        $template = new TwigTemplate(
                \dirname(__FILE__) . '/retro.html',
                array('app' => single::app(), 'message' => 'Welcome to codesaur test application.'));
        
        $template->addFilter('link', function($string, $params = []) { return single::link($string, $params); });
        
        return $template;
    }
}
