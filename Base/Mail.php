<?php namespace codesaur\Base;

class Mail extends Base
{
    public $sender;
    public $to;
    public $subject;
    public $message;
    
    public function send() : bool
    {
        if ( ! isset($this->sender)) {
            throw new \Exception('Mail sender must be set!');
        } elseif (\is_array($this->sender)) {
            $sender = '=?UTF-8?B?' . \base64_encode($this->sender[0]) . '?= <' . $this->sender[1] . '>';
        }
  
        if ( ! isset($this->to)) {
            throw new \Exception('Mail recipient must be set!');
        } elseif (\is_array($this->to)) {
            $recipient = '=?UTF-8?B?' . \base64_encode($this->to[0]) . '?= <' . $this->to[1] . '>';            
        }
        
        if (empty($this->message)) {
            throw new \Exception('No message? Are u kidding? Mail message must be set!');
        }
        
        $content_type = \substr_count($this->message, '</') >= 1 ? 'text/html' : 'text/plain';
        
        $subject = '=?UTF-8?B?' . \base64_encode($this->subject) . '?=';
        
        $header  = 'MIME-Version: 1.0' . "\r\n";
        $header .= 'Content-type: ' . $content_type . '; charset=utf-8' . "\r\n";
        $header .= 'Content-Transfer-Encoding: base64' . "\r\n";
        $header .= 'Date: ' . \date('r (T)') . "\r\n";
        
        $from =  $sender ?? $this->sender;
        $header .= 'From: ' . $from . "\r\n";
        $header .= 'Reply-To: ' . $from . "\r\n";
        
        $header .= 'X-Mailer: PHP/' . \phpversion();
        
        if (\mail($recipient ?? $this->to, $subject, \base64_encode($this->message), $header)) {
            return true;
        } else {
            return false;
        }
    }
}
