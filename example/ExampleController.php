<?php namespace erketu\Example;

use codesaur\Http\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        (new RetroTemplate())->render();
    }
    
    public function response() : ExampleResponse
    {
        return parent::response();
    }
    
    public function hello(string $firstname)
    {
        $template = new RetroTemplate($firstname);
        if ($this->request()->hasUrlParam('lastname')) {
            $template->enhance('user', ' ' .  $this->request()->getUrlParam('lastname'));
        }
        
        $template->render();
    }
    
    public function post_put()
    {
        $payload = $this->request()->getBodyJson();

        if (empty($payload->firstname)) {
            return $this->response()->error('Invalid request!');
        }

        $template = new RetroTemplate($payload->firstname);
        if ( ! empty($payload->lastname)) {
            $template->enhance('user', " $payload->lastname");
        }
        
        $template->render();
    }
}
