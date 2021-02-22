<?php

namespace erketu\Example;

use Throwable;
use Exception;

use codesaur\Http\Error\ExceptionHandlerInterface;
use codesaur\Http\Message\ReasonPrhaseInterface;
use codesaur\Template\FileTemplate;

class ExampleError implements ExceptionHandlerInterface
{
    public function exception(Throwable $throwable)
    {
        $code = $throwable->getCode();
        $message = $throwable->getMessage();
        $title = $throwable instanceof Exception ? 'Exception' : 'Error';
        
        if ($code !== 0) {
            $status = "STATUS_$code";
            $reasonPhraseInterface = ReasonPrhaseInterface::class;
            if (defined("$reasonPhraseInterface::$status")
                    && !headers_sent()
            ) {
                http_response_code($code);
            }
            
            $title .= " $code";
        }
        
        error_log("$title: $message");
        
        (new FileTemplate(\dirname(__FILE__) . '/colorlib.html', array(
            'code' => $code, 'title' => $title, 'message' => $message)))->render();
    }
}
