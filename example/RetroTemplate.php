<?php namespace erketu\Example;

use codesaur\HTML\FileTemplate;

class RetroTemplate extends FileTemplate
{
    function __construct($user = null)
    {
        parent::__construct(\dirname(__FILE__) . '/retro.html',
                array('user' => $user, 'message' => 'Welcome to codesaur example application.'));
    }
}
