<?php namespace erketu\Example;

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
                array('request' => $this->request()));
        
        $template->set('message', 'Welcome to codesaur example application.');
        
        $template->addFilter('link', function($name, $params = [])
        {
            $url = $this->app()->router()->generate($name, $params);

            if (empty($url)) {
                return 'javascript:;';
            }

            return $this->request()->getPathComplete() . $url[0];
        });
        
        return $template;
    }
}
