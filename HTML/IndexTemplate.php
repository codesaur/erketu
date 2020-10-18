<?php namespace codesaur\HTML;

abstract class IndexTemplate extends TwigTemplate
{
    function __construct(string $template = null, array $vars = null)
    {
        parent::__construct($template, $vars);
        
        $this->set('meta', array());
        $this->set('stylesheet', array());
        $this->set('plugin', array());
    }
    
    public function title(string $value)
    {
        if ( ! $this->isEmpty($value)) {
            $this->get('meta')['title'] = $value;
        }
        
        return $this;
    }
    
    public function stylesheet($style, $attr = null)
    {
        $css = array('href' => $style);
        
        if (isset($attr)) {
            $css['attr'] = $attr;
        }
        
        $this->get('stylesheet')[] = $css;
    }

    public function plugin($script, $attr = null)
    {
        $js = array('src' => $script);
        
        if (isset($attr)) {
            $js['attr'] = $attr;
        }
        
        $this->get('plugin')[] = $js;
    }
    
    public function javascript($body)
    {
        $this->enhance('javascript', $this->stringify($body));
    }
    
    public function render($content = null)
    {
        if (isset($content)) {
            $this->addContent($content);
        }
        
        $this->set('content', $this->stringify($this->get('content')));
        
        parent::render();
    }
    
    public function hasContent() : bool
    {
        return $this->get('content') instanceof Template;
    }

    public function addContent($content)
    {
        $this->setContentIndex($content);
        
        if ($this->hasContent()) {
            $this->get('content')->enhance('content', $this->stringify($content));
        } else {
            $this->enhance('content', $content);
        }
        
        return $this;
    }

    public function setContentVar(string $key, $value)
    {
        if ($this->hasContent()) {
            $this->get('content')->set($key, $value);
        } else {
            $this->set($key, $value);
        }
    }
    
    public function setContentIndex($content)
    {
        if ($content instanceof TwigTemplate) {
            $content->set('index', $this);
            
            return $this->setContentIndex($content->get('content'));
        }
    }
}
