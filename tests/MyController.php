<?php namespace My\Test\App;

use codesaur\Http\Controller;
use codesaur\HTML\TwigTemplate;

class MyController extends Controller
{
    public function index()
    {
        $this->getTemplate()->render();
    }
    
    public function hello(string $user)
    {
        $template = $this->getTemplate();
        $template->set('user', $user);
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
                array('message' => 'Welcome to codesaur test application.'));
        
        $template->addFilter('link', function($string, $params = []) { return \codesaur::link($string, $params); });
        
        return $template;
    }
}
