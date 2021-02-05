<?php declare(strict_types=1);

namespace codesaur\Http;

use codesaur\Base\ErrorHandlerInterface;
use codesaur\Http\Message\ReasonPrhaseInterface;

use Throwable;
use Exception;

class DefaultErrorHandler implements ErrorHandlerInterface
{
    public function error(Throwable $throwable)
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
        
        $host = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';
        $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

        echo    '<!doctype html>'
                . '<html lang="en">'
                . "<head><meta charset=\"utf-8\"><title>$title</title></head>"
                . "<body><h1>$title</h1><p>$message</p><hr><a href=\"$host\">$host</a></body>"
                . '</html>'; 
    }
}
