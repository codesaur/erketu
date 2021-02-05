<?php

namespace erketu\Example;

use Throwable;
use Exception;

use codesaur\Base\ErrorHandlerInterface;
use codesaur\Http\Message\ReasonPrhaseInterface;
use codesaur\Template\FileTemplate;

class ExampleError implements ErrorHandlerInterface
{
    public function error(Throwable $throwable)
    {
        $vars = array(
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
            'title' => $throwable instanceof Exception ? 'Exception' : 'Error'
        );
        
        if ($vars['code'] !== 0) {
            $status = 'STATUS_' . $vars['code'];
            $reasonPhraseInterface = ReasonPrhaseInterface::class;
            if (defined("$reasonPhraseInterface::$status")
                    && !headers_sent()
            ) {
                http_response_code($vars['code']);
            }
            
            $vars['title'] .= ' ' .  $vars['code'];
        }
        
        error_log($vars['title'] . ': ' . $vars['message']);
        
        (new FileTemplate(\dirname(__FILE__) . '/colorlib.html', $vars))->render();
    }
}
