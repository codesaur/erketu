<?php namespace erketu\Example;

use codesaur\HTML\Template;

// credits to html template
// Author: Robin Selmer
// August 22, 2017
// RETRO PAGE - Hacker themed page

class RetroTemplate extends Template
{
    function __construct($user = null)
    {
        parent::__construct(\dirname(__FILE__) . '/retro.html',
                array('user' => $user, 'message' => 'Welcome to codesaur example application.'));
    }
}
