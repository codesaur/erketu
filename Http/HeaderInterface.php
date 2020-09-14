<?php namespace codesaur\Http;
 
interface HeaderInterface
{
    public function location(string $url);    
    public function redirect(string $url, int $status = 302);
    
    public function respond(int $status = null);
    public function respond404();
    public function respondOK();
    public function response(string $msg);
    
    public function sent(string &$file = null, int &$line = null) : bool;
    
    public function status() : int;
    
    public function content(string $type);

    public function goBack();
    public function goBackJS(int $number = 1);
    
    public function getEntityBody();
    public function getEntityBodyJson();    
}
