<?php namespace codesaur\HTML;

class HTML5 implements HTML5Interface
{
    // HTML5 a
    // -------
    // The <a> tag defines a hyperlink, which is used to link from one page to another.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // download       filename         Specifies that the target will be downloaded when a user clicks on the hyperlink
    // href           URL              Specifies the URL of the page the link goes to
    // hreflang       language_code    Specifies the language of the linked document
    // media          media_query      Specifies what media/device the linked document is optimized for
    // rel            alternate        Specifies the relationship between the current document and the linked document
    //                author
    //                bookmark
    //                help
    //                license
    //                next
    //                nofollow
    //                noreferrer
    //                prefetch
    //                prev
    //                search
    //                tag
    // target         _blank           Specifies where to open the linked document
    //                _parent
    //                _self
    //                _top
    //                framename
    // type           media_type       Specifies the media type of the linked document
    public static function a(array $attr = [], $inner = '', bool $close = true) : string
    {
        $custom  = (isset($attr['download'])) ? ' download="' . $attr['download'] . '"' : '';
        $custom .= (isset($attr['href'])) ? ' href="' . $attr['href'] . '"' : '';
        $custom .= (isset($attr['hreflang'])) ? ' hreflang="' . $attr['hreflang'] . '"' : '';
        $custom .= (isset($attr['media'])) ? ' media="' . $attr['media'] . '"' : '';
        $custom .= (isset($attr['rel'])) ? ' rel="' . $attr['rel'] . '"' : '';
        $custom .= (isset($attr['target'])) ? ' target="' . $attr['target'] . '"' : '';
        $custom .= (isset($attr['type'])) ? ' type="' . $attr['type'] . '"' : '';
        $custom .= (isset($attr['onclick'])) ? ' onclick="' . $attr['onclick'] . '"' : ''; 
        
        return self::element('a', $attr, $custom, $inner, $close);
    }

    // HTML5 u
    // -------
    // The <u> tag represents some text that should be stylistically different from normal text, such as misspelled words or proper nouns in Chinese.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function u($inner = '', array $attr = [], bool $close = true) : string
    {
        return self::element('u', $attr, '', $inner, $close);
    }

    // HTML5 br
    // --------
    // The <br> tag inserts a single line break.
    // The <br> tag is an empty tag which means that it has no end tag.
    // In HTML, the <br> tag has no end tag.
    // In XHTML, the <br> tag must be properly closed, like this: <br />.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function br(array $attr = [], bool $xhtml = true) : string
    {
        return self::element('br', $attr, ($xhtml) ? ' /' : '');
    }

    // HTML5 hr
    // --------
    // The <hr> tag defines a thematic break in an HTML page (e.g. a shift of topic).
    // The <hr> element is used to separate content (or define a change) in an HTML page.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function hr(array $attr = [], bool $xhtml = true) : string
    {
        return self::element('hr', $attr, ($xhtml) ? ' /' : '');
    }

    // HTML5 Form
    // ----------
    // The <form> tag is used to create an HTML form for user input
    // In XHTML, the name attribute is deprecated. Use the global id attribute instead
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // accept-charset character_set    Specifies the character encodings that are to be used for the form submission
    // action	      URL              Specifies where to send the form-data when a form is submitted
    // autocomplete   on/off           Specifies whether a form should have autocomplete on or off
    // enctype        application/x-www-form-urlencoded
    //                multipart/form-data
    //                text/plain       Specifies how the form-data should be encoded when submitting it to the server (only for method="post")
    // method         get/post         Specifies the HTTP method to use when sending form-data
    // name           text             Specifies the name of a form
    // novalidate     novalidate       Specifies that the form should not be validated when submitted
    // target         _blank
    //                _self
    //                _parent
    //                _top             Specifies where to display the response that is received after submitting the form
    public static function form(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string
    {
        $custom  = (isset($attr['accept-charset'])) ? ' accept-charset="' . $attr['accept-charset'] . '"' : '';
        $custom .= (isset($attr['action'])) ? ' action="' . $attr['action'] . '"' : '';
        $custom .= (isset($attr['autocomplete'])) ? ' autocomplete="' . $attr['autocomplete'] . '"' : '';
        $custom .= (isset($attr['name']) && ! $xhtml) ? ' name="' . $attr['name'] . '"' : '';
        if (isset($attr['novalidate']) && $attr['novalidate']) {
            $custom .= " novalidate" . (($xhtml) ? '="novalidate"' : '');
        }
        $custom .= (isset($attr['target'])) ? ' target="' . $attr['target'] . '"' : '';
        if (isset($attr['method'])) {
            $custom .= ' method="' . $attr['method'] . '"'; 
            if (isset($attr['enctype']) && \strtolower($attr['method']) == 'post') {
                $custom .= ' enctype="' . $attr['enctype'] . '"'; 
            }
        }
        
        return self::element('form', $attr, $custom, $inner, $close);
    }

    // HTML5 Div
    // ----------
    // The <div> tag defines a division or a section in an HTML document.
    // The <div> tag is used to group block-elements to format them with CSS.
    public static function div(array $attr = [], $inner = '', bool $close = true) : string
    {
        return self::element('div', $attr, '', $inner, $close);
    }
    
    public static function divc($content, $class = 'row') : string
    {
        $div = "<div class=\"$class\">";        
        if ($content instanceof View ||
                $content instanceof Template) {
            $div .= $content->output();
        } else {
            $div .= self::inline($content);
        }        
        $div .= '</div>';
        
        return $div;
    }
    
    // HTML5 Img
    // ---------
    // The <img> tag defines an image in an HTML page.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // alt            text             Specifies an alternate text for an image
    // crossorigin    anonymous        Allow images from third-party sites that allow cross-origin access to be used with canvas
    //                use-credentials
    // height         pixels           Specifies the height of an image
    // ismap          ismap            Specifies an image as a server-side image-map
    // longdesc       URL              Specifies a URL to a detailed description of an image
    // src            URL              Specifies the URL of an image
    // usemap         #mapname         Specifies an image as a client-side image-map
    // width          pixels           Specifies the width of an image
    public static function img(array $attr = [], bool $xhtml = true) : string
    {
        $custom  = (isset($attr['alt'])) ? ' alt="' . $attr['alt'] . '"' : '';
        $custom .= (isset($attr['crossorigin'])) ? ' crossorigin="' . $attr['crossorigin'] . '"' : '';
        $custom .= (isset($attr['height'])) ? ' height="' . $attr['height'] . '"' : '';
        if (isset($attr['ismap']) && $attr['ismap']) {
            $custom .= " ismap" . (($xhtml) ? '="ismap"' : '');
        }
        $custom .= (isset($attr['longdesc'])) ? ' longdesc="' . $attr['longdesc'] . '"' : '';
        $custom .= (isset($attr['src'])) ? ' src="' . $attr['src'] . '"' : '';
        $custom .= (isset($attr['usemap'])) ? ' usemap="' . $attr['usemap'] . '"' : '';
        $custom .= (isset($attr['width'])) ? ' width="' . $attr['width'] . '"' : '';
        
        return self::element('img', $attr, $custom);
    }

    // HTML5 Input
    // -----------
    // In HTML, the <input> tag has no end tag.
    // In XHTML, the <input> tag must be properly closed, like this <input />.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // accept         file_extension   Specifies the types of files that the server accepts (only for type="file")
    //                audio/*
    //                video/*
    //                image/*
    //                media_type    
    // alt            text             Specifies an alternate text for images (only for type="image")
    // autocomplete   on/off           Specifies whether an <input> element should have autocomplete enabled
    // autofocus      autofocus        Specifies that an <input> element should automatically get focus when the page loads
    // checked        checked          Specifies that an <input> element should be pre-selected when the page loads (for type="checkbox" or type="radio")
    // disabled       disabled         Specifies that an <input> element should be disabled
    // form           form_id          Specifies one or more forms the <input> element belongs to
    // formaction     URL              Specifies the URL of the file that will process the input control when the form is submitted (for type="submit" and type="image")
    // formenctype    application/x-www-form-urlencoded
    //                multipart/form-data
    //                text/plain       Specifies how the form-data should be encoded when submitting it to the server (for type="submit" and type="image")
    // formmethod     get/post         Defines the HTTP method for sending data to the action URL (for type="submit" and type="image")
    // formnovalidate formnovalidate   Defines that form elements should not be validated when submitted
    // formtarget     _blank
    //                _self
    //                _parent
    //                _top
    //                framename        Specifies where to display the response that is received after submitting the form (for type="submit" and type="image")
    // height         pixels           Specifies the height of an <input> element (only for type="image")
    // list           datalist_id      Refers to a <datalist> element that contains pre-defined options for an <input> element
    // max            number / date    Specifies the maximum value for an <input> element
    // maxlength      number           Specifies the maximum number of characters allowed in an <input> element
    // min            number / date    Specifies a minimum value for an <input> element
    // multiple       multiple         Specifies that a user can enter more than one value in an <input> element
    // pattern        regexp           Specifies a regular expression that an <input> element's value is checked against
    // placeholder    text             Specifies a short hint that describes the expected value of an <input> element
    // readonly       readonly         Specifies that an input field is read-only
    // required       required         Specifies that an input field must be filled out before submitting the form
    // size           number           Specifies the width, in characters, of an <input> element
    // src            URL              Specifies the URL of the image to use as a submit button (only for type="image")
    // step           number           Specifies the legal number intervals for an input field
    // tabindex       number
    // type           button
    //                checkbox
    //                color
    //                date 
    //                datetime
    //                datetime-local
    //                email
    //                file
    //                hidden
    //                image
    //                month
    //                number
    //                password
    //                radio
    //                range
    //                reset
    //                search
    //                submit
    //                tel
    //                text
    //                time 
    //                url
    //                week             Specifies the type <input> element to display
    // value          text             Specifies the value of an <input> element
    // width          pixels           Specifies the width of an <input> element (only for type="image")    
    public static function input(array $attr = [], bool $xhtml = true) : string
    {
        $custom = (isset($attr['autocomplete'])) ? ' autocomplete="' . $attr['autocomplete'] . '"' : '';
        if (isset($attr['autofocus']) && $attr['autofocus']) {
            $custom .= ' autofocus' . (($xhtml) ? '="autofocus"' : '');
        }
        if (isset($attr['disabled']) && $attr['disabled']) {
            $custom .= ' disabled' . (($xhtml) ? '="disabled"' : '');
        }
        $custom .= (isset($attr['form'])) ? ' form="' . $attr['form'] . '"' : '';
        $custom .= (isset($attr['list'])) ? ' list="' . $attr['list'] . '"' : '';
        $custom .= (isset($attr['max'])) ? ' max="' . $attr['max'] . '"' : '';
        $custom .= (isset($attr['maxlength'])) ? ' maxlength="' . $attr['maxlength'] . '"' : '';
        $custom .= (isset($attr['min'])) ? ' min="' . $attr['min'] . '"' : '';
        if (isset($attr['multiple']) && $attr['multiple']) {
            $custom .= ' multiple' . (($xhtml) ? '="multiple"' : '');
        }
        $custom .= (isset($attr['name'])) ? ' name="' . $attr['name'] . '"' : '';
        $custom .= (isset($attr['pattern'])) ? ' pattern="' . $attr['pattern'] . '"' : '';
        $custom .= (isset($attr['placeholder'])) ? ' placeholder="' . $attr['placeholder'] . '"' : '';
        if (isset($attr['readonly']) && $attr['readonly']) {
            $custom .= ' readonly' . (($xhtml) ? '="readonly"' : '');
        }
        if (isset($attr['required']) && $attr['required']) {
            $custom .= ' required' . (($xhtml) ? '="required"' : '');
        }
        $custom .= (isset($attr['size'])) ? ' size="' . $attr['size'] . '"' : '';
        $custom .= (isset($attr['step'])) ? ' step="' . $attr['step'] . '"' : '';
        $custom .= (isset($attr['value'])) ? ' value="' . $attr['value'] . '"' : '';
        if (isset($attr['type'])) {
            $custom .= ' type="' . $attr['type'] . '"'; 
            if (isset($attr['accept']) && $attr['type'] == 'file') {
                $custom .= ' accept="' . $attr['accept'] . '"'; 
            }
            if ((isset($attr['checked']) && $attr['checked']) &&
                    ($attr['type'] == 'checkbox' || $attr['type'] == 'radio')) {
                $custom .= ' checked' . (($xhtml) ? '="checked"' : '');
            }
            if ($attr['type'] == 'image') {
                $custom .= (isset($attr['alt'])) ? ' alt="' . $attr['alt'] . '"' : ''; 
                $custom .= (isset($attr['src'])) ? ' src="' . $attr['src'] . '"' : '';
                $custom .= (isset($attr['height'])) ? ' height="' . $attr['height'] . '"' : '';
                $custom .= (isset($attr['width'])) ? ' width="' . $attr['width'] . '"' : '';
            }
            if ($attr['type'] == 'image' || $attr['type'] == 'submit') {
                $custom .= (isset($attr['formaction'])) ? ' formaction="' . $attr['formaction'] . '"' : ''; 
                $custom .= (isset($attr['formenctype'])) ? ' formenctype="' . $attr['formenctype'] . '"' : ''; 
                $custom .= (isset($attr['formmethod'])) ? ' formmethod="' . $attr['formmethod'] . '"' : ''; 
                $custom .= (isset($attr['formtarget'])) ? ' formtarget="' . $attr['formtarget'] . '"' : ''; 
            }
        }
        $custom .= ($xhtml) ? ' /' : '';
        
        return self::element('input', $attr, $custom);
    }
    
    // HTML5 Button
    // ------------
    // The <button> tag defines a clickable button.
    // Inside a <button> element you can put content, like text or images. This is the difference between this element and buttons created with the <input> element.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // disabled       disabled         Specifies that a button should be disabled
    // form           form_id          Specifies one or more forms the button belongs to
    // formaction     URL              Specifies where to send the form-data when a form is submitted. Only for type="submit"
    // formenctype    application/x-www-form-urlencoded
    //                multipart/form-data
    //                text/plain       Specifies how form-data should be encoded before sending it to a server. Only for type="submit"
    // formmethod     get              Specifies how to send the form-data (which HTTP method to use). Only for type="submit"
    //                post
    // formnovalidate formnovalidate   Specifies that the form-data should not be validated on submission. Only for type="submit"
    // formtarget     _blank           Specifies where to display the response after submitting the form. Only for type="submit"
    //                _self
    //                _parent
    //                _top
    //                framename
    // name           name             Specifies a name for the button
    // type           button           Specifies the type of button
    //                reset
    //                submit
    // value          text             Specifies an initial value for the button
    public static function button(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string
    {
        $custom = '';
        if (isset($attr['autofocus']) && $attr['autofocus']) {
            $custom .= ' autofocus' . (($xhtml) ? '="autofocus"' : '');
        }
        if (isset($attr['disabled']) && $attr['disabled']) {
            $custom .= ' disabled' . (($xhtml) ? '="disabled"' : '');
        }
        $custom .= (isset($attr['form'])) ? ' form="' . $attr['form'] . '"' : '';
        $custom .= (isset($attr['name'])) ? ' name="' . $attr['name'] . '"' : '';
        $custom .= (isset($attr['value'])) ? ' value="' . $attr['value'] . '"' : '';
        if (isset($attr['type'])) {
            $custom .= ' type="' . $attr['type'] . '"'; 
            if ($attr['type'] == 'submit') {
                $custom .= (isset($attr['formaction'])) ? ' formaction="' . $attr['formaction'] . '"' : ''; 
                $custom .= (isset($attr['formenctype'])) ? ' formenctype="' . $attr['formenctype'] . '"' : ''; 
                $custom .= (isset($attr['formmethod'])) ? ' formmethod="' . $attr['formmethod'] . '"' : ''; 
                if (isset($attr['formnovalidate']) && $attr['formnovalidate']) {
                    $custom .= ' formnovalidate' . (($xhtml) ? '="formnovalidate"' : '');
                }
                $custom .= (isset($attr['formtarget'])) ? ' formtarget="' . $attr['formtarget'] . '"' : ''; 
            }
        }
        
        return self::element('button', $attr, $custom, $inner, $close);
    }

    // HTML5 Label
    // -----------
    // The <label> tag defines a label for an <input> element.
    // The <label> element does not render as anything special for the user. However, it provides a usability improvement for mouse users, because if the user clicks on the text within the <label> element, it toggles the control.
    // The for attribute of the <label> tag should be equal to the id attribute of the related element to bind them together.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // for            element_id       Specifies which form element a label is bound to
    // form 	      form_id          Specifies one or more forms the label belongs to
    public static function label(array $attr = [], $inner = '', bool $close = true) : string
    {
        $custom  = (isset($attr['for'])) ? ' for="' . $attr['for'] . '"' : '';
        $custom .= (isset($attr['form'])) ? ' form="' . $attr['form'] . '"' : '';
        
        return self::element('label', $attr, $custom, $inner, $close);
    }
    
    // HTML5 P
    // -------
    // The <p> tag defines a paragraph.
    // Browsers automatically add some space (margin) before and after each <p> element. The margins can be modified with CSS (with the margin properties).
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function p(array $attr = [], $inner = '', bool $close = true) : string
    {
        return self::element('p', $attr, '', $inner, $close);
    }

    // HTML5 H1-H6
    // -----------
    // The <h1> to <h6> tags are used to define HTML headings.
    // <h1> defines the most important heading. <h6> defines the least important heading.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function h(int $h = 1, array $attr = [], $inner = '', bool $close = true) : string
    {
        if ($h < 1 || $h > 6) {
            $h = 1;
        }
        
        return self::element("h$h", $attr, '', $inner, $close);
    }

    // HTML5 Entity &nbsp;
    // -------------------
    // non-breaking space
    public static function nbsp(int $multiplier = 1) : string
    {
        return \str_repeat('&nbsp;', $multiplier);
    }

    // HTML5 ul
    // --------
    // The <ul> tag defines an unordered (bulleted) list.
    // Use the <ul> tag together with the <li> tag to create unordered lists.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function ul(array $attr = [], $inner = '', bool $close = false) : string
    {
        return self::element('ul', $attr, '', $inner, $close);
    }

    // HTML5 ol
    // --------
    // The <ol> tag defines an ordered list. An ordered list can be numerical or alphabetical.
    // Use the <li> tag to define list items.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // reversed       reversed         Specifies that the list order should be descending (9,8,7...)
    // start          number           Specifies the start value of an ordered list
    // type           1/A/a/I/i        Specifies the kind of marker to use in the list
    public static function ol(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string
    {
        $custom = (isset($attr['start'])) ? ' start="' . $attr['start'] . '"' : '';
        if (isset($attr['reversed']) && $attr['reversed']) {
            $custom .= ' reversed' . (($xhtml) ? '="reversed"' : '');
        }        
        $custom .= (isset($attr['type'])) ? ' type="' . $attr['type'] . '"' : '';
        
        return self::element('ol', $attr, $custom, $inner, $close);
    }
    
    // HTML5 li
    // --------
    // The <li> tag defines a list item.
    // The <li> tag is used in ordered lists(<ol>), unordered lists (<ul>), and in menu lists (<menu>).
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // value          number           Specifies the value of a list item. The following list items will increment from that number (only for <ol> lists)
    public static function li(array $attr = [], $inner = '', bool $close = true) : string
    {
        $custom = (isset($attr['value'])) ? ' value="' . $attr['value'] . '"' : '';
        
        return self::element('li', $attr, $custom, $inner, $close);
    }

    // HTML5 Select
    // ------------
    // The <select> element is used to create a drop-down list.
    // The <option> tags inside the <select> element define the available options in the list.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // autofocus      autofocus        Specifies that the drop-down list should automatically get focus when the page loads
    // disabled       disabled         Specifies that a drop-down list should be disabled
    // form           form_id          Defines one or more forms the select field belongs to
    // multiple       multiple         Specifies that multiple options can be selected at once
    // name           name             Defines a name for the drop-down list
    // required       required         Specifies that the user is required to select a value before submitting the form
    // size           number           Defines the number of visible options in a drop-down list
    public static function select(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string
    {
        $custom = '';
        if (isset($attr['autofocus']) && $attr['autofocus']) {
            $custom .= ' autofocus' . (($xhtml) ? '="autofocus"' : '');
        }
        if (isset($attr['disabled']) && $attr['disabled']) {
            $custom .= ' disabled' . (($xhtml) ? '="disabled"' : '');
        }
        $custom .= (isset($attr['form'])) ? ' form="' . $attr['form'] . '"' : '';
        if (isset($attr['multiple']) && $attr['multiple']) {
            $custom .= ' multiple' . (($xhtml) ? '="multiple"' : '');
        }
        $custom .= (isset($attr['name'])) ? ' name="' . $attr['name'] . '"' : '';        
        if (isset($attr['required']) && $attr['required']) {
            $custom .= ' required' . (($xhtml) ? '="required"' : '');
        }
        $custom .= (isset($attr['size'])) ? ' size="' . $attr['size'] . '"' : '';
        
        return self::element('select', $attr, $custom, $inner, $close);
    }

    // HTML5 Option
    // ------------
    // The <option> tag defines an option in a select list.
    // <option> elements go inside a <select> or <datalist> element.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // disabled       disabled         Specifies that an option should be disabled
    // label          text             Specifies a shorter label for an option
    // selected       selected         Specifies that an option should be pre-selected when the page loads
    // value          text             Specifies the value to be sent to a server
    public static function option(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string
    {
        $custom = '';
        if (isset($attr['disabled']) && $attr['disabled']) {
            $custom .= ' disabled' . (($xhtml) ? '="disabled"' : '');
        }
        $custom .= (isset($attr['label'])) ? ' label="' . $attr['label'] . '"' : '';
        if (isset($attr['selected']) && $attr['selected']) {
            $custom .= ' selected' . (($xhtml) ? '="selected"' : '');
        }
        $custom .= (isset($attr['value'])) ? ' value="' . $attr['value'] . '"' : '';
        
        return self::element('option', $attr, $custom, $inner, $close);
    }
    
    // HTML5 Script
    // ------------
    // The <script> tag is used to define a client-side script (JavaScript).
    // The <script> element either contains scripting statements, or it points to an external script file through the src attribute.
    // Common uses for JavaScript are image manipulation, form validation, and dynamic changes of content.
    // Note: If the "src" attribute is present, the <script> element must be empty.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // async          async            Specifies that the script is executed asynchronously (only for external scripts)
    // charset        charset          Specifies the character encoding used in an external script file
    // defer          defer            Specifies that the script is executed when the page has finished parsing (only for external scripts)
    // src            URL              Specifies the URL of an external script file
    // type           media_type       Specifies the media type of the script
    public static function script(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string
    {
        $custom = '';
        if (isset($attr['async']) && $attr['async']) {
            $custom .= ' async' . (($xhtml) ? '="async"' : '');
        }
        $custom .= (isset($attr['charset'])) ? ' charset="' . $attr['charset'] . '"' : '';
        if (isset($attr['defer']) && $attr['defer']) {
            $custom .= ' defer' . (($xhtml) ? '="defer"' : '');
        }
        $custom .= (isset($attr['src'])) ? ' src="' . $attr['src'] . '"' : '';
        $custom .= (isset($attr['type'])) ? ' type="' . $attr['type'] . '"' : '';
        
        return self::element('select', $attr, $custom, $inner, $close);
    }
    
    // HTML5 Pre
    // ---------
    // The <pre> tag defines preformatted text.
    // Text in a <pre> element is displayed in a fixed-width font (usually Courier), and it preserves both spaces and line breaks.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function pre(array $attr = [], $inner = '', bool $close = true) : string
    {
        return self::element('pre', $attr, '', $inner, $close);
    }

    // HTML5 Span
    // ----------
    // The <span> tag is used to group inline-elements in a document.
    // The <span> tag provides no visual change by itself.
    // The <span> tag provides a way to add a hook to a part of a text or a part of a document.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function span(array $attr = [], $inner = '', bool $close = true) : string
    {
        return self::element('span', $attr, '', $inner, $close);
    }

    // HTML5 Textarea
    // --------------
    // The <label> tag defines a label for an <input> element.
    // The <label> element does not render as anything special for the user. However, it provides a usability improvement for mouse users, because if the user clicks on the text within the <label> element, it toggles the control.
    // The for attribute of the <label> tag should be equal to the id attribute of the related element to bind them together.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // autofocus      autofocus        Specifies that a text area should automatically get focus when the page loads
    // cols           number           Specifies the visible width of a text area
    // dirname        textareaname.dir Specifies that the text direction of the textarea will be submitted
    // disabled       disabled         Specifies that a text area should be disabled
    // form           form_id          Specifies one or more forms the text area belongs to
    // maxlength      number           Specifies the maximum number of characters allowed in the text area
    // name           text             Specifies a name for a text area
    // placeholder    text             Specifies a short hint that describes the expected value of a text area
    // readonly       readonly         Specifies that a text area should be read-only
    // required       required         Specifies that a text area is required/must be filled out
    // rows           number           Specifies the visible number of lines in a text area
    // wrap           hard/soft        Specifies how the text in a text area is to be wrapped when submitted in a form
    public static function textarea(array $attr = [], $inner = '', bool $close = true, bool $xhtml = true) : string
    {
        $custom = '';
        if (isset($attr['autofocus']) && $attr['autofocus']) {
            $custom .= ' autofocus' . (($xhtml) ? '="autofocus"' : '');
        }
        $custom .= (isset($attr['cols'])) ? ' cols="' . $attr['cols'] . '"' : '';
        $custom .= (isset($attr['dirname'])) ? ' dirname="' . $attr['dirname'] . '"' : '';
        if (isset($attr['disabled']) && $attr['disabled']) {
            $custom .= ' disabled' . (($xhtml) ? '="disabled"' : '');
        }
        $custom .= (isset($attr['form'])) ? ' form="' . $attr['form'] . '"' : '';
        $custom .= (isset($attr['maxlength'])) ? ' maxlength="' . $attr['maxlength'] . '"' : '';
        $custom .= (isset($attr['name'])) ? ' name="' . $attr['name'] . '"' : '';
        $custom .= (isset($attr['placeholder'])) ? ' placeholder="' . $attr['placeholder'] . '"' : '';
        if (isset($attr['readonly']) && $attr['readonly']) {
            $custom .= ' readonly' . (($xhtml) ? '="readonly"' : '');
        }
        if (isset($attr['required']) && $attr['required']) {
            $custom .= ' required' . (($xhtml) ? '="required"' : '');
        }
        $custom .= (isset($attr['rows'])) ? ' rows="' . $attr['rows'] . '"' : '';
        $custom .= (isset($attr['wrap'])) ? ' wrap="' . $attr['wrap'] . '"' : '';
        
        return self::element('textarea', $attr, $custom, $inner, $close);
    }

    // HTML5 table
    // -----------
    // The <table> tag defines an HTML table.
    // An HTML table consists of the <table> element and one or more <tr>, <th>, and <td> elements.
    // The <tr> element defines a table row, the <th> element defines a table header, and the <td> element defines a table cell.
    // A more complex HTML table may also include <caption>, <col>, <colgroup>, <thead>, <tfoot>, and <tbody> elements.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // border         1/0              Specifies whether or not the table is being used for layout purposes
    // sortable       sortable         Specifies that the table should be sortable
    public static function table(array $attr = [], $inner = '', bool $close = false, bool $xhtml = true) : string
    {
        $custom = (isset($attr['border'])) ? ' border="' . $attr['border'] . '"' : '';
        if (isset($attr['sortable']) && $attr['sortable']) {
            $custom .= ' sortable' . (($xhtml) ? '="sortable"' : '');
        }
        
        return self::element('table', $attr, '', $inner, $close);
    }

    // HTML5 tr
    // --------
    // The <tr> tag defines a row in an HTML table.
    // A <tr> element contains one or more <th> or <td> elements.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    public static function tr(array $attr = [], $inner = '', bool $close = false) : string
    {
        return self::element('tr', $attr, '', $inner, $close);
    }

    // HTML5 th / td
    // -------------
    // The <th> tag defines a header cell in an HTML table.
    // The <td> tag defines a standard cell in an HTML table.
    // An HTML table has two kinds of cells:
    //   Header cells - contains header information (created with the <th> element)
    //   Standard cells - contains data (created with the <td> element)
    // The text in <th> elements are bold and centered by default.
    // The text in <td> elements are regular and left-aligned by default.
    // *********      *****            ****
    // Attribute      Value            Desc
    // *********      *****            ****
    // colspan        number           Specifies the number of columns a cell should span
    // headers        header_id        Specifies one or more header cells a cell is related to
    public static function table_cell(string $cell, array $attr = [], $inner = '', bool $close = true) : string
    {
        $custom  = (isset($attr['colspan'])) ? ' colspan="' . $attr['colspan'] . '"' : '';
        $custom .= (isset($attr['headers'])) ? ' headers="' . $attr['headers'] . '"' : '';
        
        return self::element($cell, $attr, $custom, $inner, $close);
    }
    
    public static function th(array $attr = [], $inner = '', bool $close = true) : string
    {
        return self::table_cell('th', $attr, $inner, $close);
    }
    
    public static function td(array $attr = [], $inner = '', bool $close = true) : string
    {
        return self::table_cell('td', $attr, $inner, $close);
    }

    // HTML5 Global Attributes
    // -----------------------
    // HTML attributes give elements meaning and context.
    // The global attributes below can be used on any HTML element.
    // *********       ****
    // Attribute       Desc
    // *********       ****
    // accesskey       Specifies a shortcut key to activate/focus an element
    // class           Specifies one or more classnames for an element (refers to a class in a style sheet)
    // contenteditable Specifies whether the content of an element is editable or not
    // contextmenu     Specifies a context menu for an element. The context menu appears when a user right-clicks on the element
    // data-*          Used to store custom data private to the page or application
    // dir             Specifies the text direction for the content in an element
    // draggable       Specifies whether an element is draggable or not
    // dropzone        Specifies whether the dragged data is copied, moved, or linked, when dropped
    // hidden          Specifies that an element is not yet, or is no longer, relevant
    // id              Specifies a unique id for an element
    // lang            Specifies the language of the element's content
    // spellcheck      Specifies whether the element is to have its spelling and grammar checked or not
    // style           Specifies an inline CSS style for an element
    // tabindex        Specifies the tabbing order of an element
    // title           Specifies extra information about an element
    // translate       Specifies whether the content of an element should be translated or not
    public static function global_attributes(array $attr = []) : string
    {
        if (empty($attr)) {
            return '';
        }
        
        $html  = (isset($attr['accesskey'])) ? ' accesskey="' . $attr['accesskey'] . '"' : '';
        $html .= (isset($attr['class'])) ? ' class="' . $attr['class'] . '"' : '';
        $html .= (isset($attr['contenteditable'])) ? ' contenteditable="' . $attr['contenteditable'] . '"' : '';
        $html .= (isset($attr['contextmenu'])) ? ' contextmenu="' . $attr['contextmenu'] . '"' : '';
        $html .= (isset($attr['dir'])) ? ' dir="' . $attr['dir'] . '"' : '';
        $html .= (isset($attr['draggable'])) ? ' draggable="' . $attr['draggable'] . '"' : '';
        $html .= (isset($attr['dropzone'])) ? ' dropzone="' . $attr['dropzone'] . '"' : '';
        $html .= (isset($attr['hidden']) && $attr['hidden']) ? ' hidden' : '';
        $html .= (isset($attr['id'])) ? ' id="' . $attr['id'] . '"' : '';
        $html .= (isset($attr['role'])) ? ' role="' . $attr['role'] . '"' : '';
        $html .= (isset($attr['lang'])) ? ' lang="' . $attr['lang'] . '"' : '';
        $html .= (isset($attr['spellcheck'])) ? ' spellcheck="' . $attr['spellcheck'] . '"' : '';
        $html .= (isset($attr['style'])) ? ' style="' . $attr['style'] . '"' : '';
        $html .= (isset($attr['tabindex'])) ? ' tabindex="' . $attr['tabindex'] . '"' : '';
        $html .= (isset($attr['title'])) ? ' title="' . $attr['title'] . '"' : '';
        $html .= (isset($attr['translate'])) ? ' translate="' . $attr['translate'] . '"' : '';
        
        $html .= (isset($attr['data-src'])) ? ' data-src="' . $attr['data-src'] . '"' : '';
        $html .= (isset($attr['data-rel'])) ? ' data-rel="' . $attr['data-rel'] . '"' : '';
        $html .= (isset($attr['data-target'])) ? ' data-target="' . $attr['data-target'] . '"' : '';
        $html .= (isset($attr['data-toggle'])) ? ' data-toggle="' . $attr['data-toggle'] . '"' : '';
        $html .= (isset($attr['data-provide'])) ? ' data-provide="' . $attr['data-provide'] . '"' : '';        
        $html .= (isset($attr['data-remote'])) ? ' data-remote="' . $attr['data-remote'] . '"' : '';
        $html .= (isset($attr['data-dismiss'])) ? ' data-dismiss="' . $attr['data-dismiss'] . '"' : '';
        $html .= (isset($attr['data-display'])) ? ' data-display="' . $attr['data-display'] . '"' : '';
        $html .= (isset($attr['data-provides'])) ? ' data-provides="' . $attr['data-provides'] . '"' : '';
        $html .= (isset($attr['data-trigger'])) ? ' data-trigger="' . $attr['data-trigger'] . '"' : '';
        $html .= (isset($attr['data-filter'])) ? ' data-filter="' . $attr['data-filter'] . '"' : '';
        $html .= (isset($attr['data-original-title'])) ? ' data-original-title="' . $attr['data-original-title'] . '"' : '';
        
        if (isset($attr['custom'])) {
            $html .= ' ' . $attr['custom'];
        }
        
        return $html;
    }
    
    // HTML5 Event Attributes
    // -----------------------
    // Window Event Attributes
    // Events triggered for the window object (applies to the <body> tag)
    // onafterprint    Script to be run after the document is printed
    // onbeforeprint   Script to be run before the document is printed
    // onbeforeunload  Script to be run when the document is about to be unloaded
    // onerror         Script to be run when an error occurs
    // onhashchange    Script to be run when there has been changes to the anchor part of the a URL
    // onload          Fires after the page is finished loading
    // onmessage       Script to be run when the message is triggered
    // onoffline       Script to be run when the browser starts to work offline
    // ononline        Script to be run when the browser starts to work online
    // onpagehide      Script to be run when a user navigates away from a page
    // onpageshow      Script to be run when a user navigates to a page
    // onpopstate      Script to be run when the window's history changes
    // onresize        Fires when the browser window is resized
    // onstorage       Script to be run when a Web Storage area is updated
    // onunload        Fires once a page has unloaded (or the browser window has been closed)
    // -----------
    // Form Events
    // Events triggered by actions inside a HTML form (applies to almost all HTML elements, but is most used in form elements)
    // onblur	       Fires the moment that the element loses focus
    // onchange	       Fires the moment when the value of the element is changed
    // oncontextmenu   Script to be run when a context menu is triggered
    // onfocus         Fires the moment when the element gets focus
    // oninput         Script to be run when an element gets user input
    // oninvalid       Script to be run when an element is invalid
    // onreset         Fires when the Reset button in a form is clicked
    // onsearch        Fires when the user writes something in a search field (for <input="search">)
    // onselect        Fires after some text has been selected in an element
    // onsubmit        Fires when a form is submitted
    // ---------------
    // Keyboard Events
    // onkeydown       Fires when a user is pressing a key
    // onkeypress      Fires when a user presses a key
    // onkeyup         Fires when a user releases a key
    // ------------
    // Mouse Events
    // Events triggered by a mouse, or similar user actions
    // onclick         Fires on a mouse click on the element
    // ondblclick      Fires on a mouse double-click on the element
    // ondrag          Script to be run when an element is dragged
    // ondragend       Script to be run at the end of a drag operation
    // ondragenter     Script to be run when an element has been dragged to a valid drop target
    // ondragleave     Script to be run when an element leaves a valid drop target
    // ondragover      Script to be run when an element is being dragged over a valid drop target
    // ondragstart     Script to be run at the start of a drag operation
    // ondrop          Script to be run when dragged element is being dropped
    // onmousedown     Fires when a mouse button is pressed down on an element
    // onmousemove     Fires when the mouse pointer is moving while it is over an element
    // onmouseout      Fires when the mouse pointer moves out of an element
    // onmouseover     Fires when the mouse pointer moves over an element
    // onmouseup       Fires when a mouse button is released over an element
    // onmousewheel    Deprecated. Use the onwheel attribute instead
    // onscroll        Script to be run when an element's scrollbar is being scrolled
    // onwheel         Fires when the mouse wheel rolls up or down over an element
    // ----------------
    // Clipboard Events
    // oncopy          Fires when the user copies the content of an element
    // oncut           Fires when the user cuts the content of an element
    // onpaste         Fires when the user pastes some content in an element
    // ------------
    // Media Events
    // Events triggered by medias like videos, images and audio (applies to all HTML elements, but is most common in media elements, like <audio>, <embed>, <img>, <object>, and <video>)
    // onabort          Script to be run on abort
    // oncanplay        Script to be run when a file is ready to start playing (when it has buffered enough to begin)
    // oncanplaythrough Script to be run when a file can be played all the way to the end without pausing for buffering
    // oncuechange      Script to be run when the cue changes in a <track> element
    // ondurationchange Script to be run when the length of the media changes
    // onemptied        Script to be run when something bad happens and the file is suddenly unavailable (like unexpectedly disconnects)
    // onended          Script to be run when the media has reach the end (a useful event for messages like "thanks for listening")
    // onerror          Script to be run when an error occurs when the file is being loaded
    // onloadeddata     Script to be run when media data is loaded
    // onloadedmetadata Script to be run when meta data (like dimensions and duration) are loaded
    // onloadstart      Script to be run just as the file begins to load before anything is actually loaded
    // onpause          Script to be run when the media is paused either by the user or programmatically
    // onplay           Script to be run when the media is ready to start playing
    // onplaying        Script to be run when the media actually has started playing
    // onprogress       Script to be run when the browser is in the process of getting the media data
    // onratechange     Script to be run each time the playback rate changes (like when a user switches to a slow motion or fast forward mode)
    // onseeked         Script to be run when the seeking attribute is set to false indicating that seeking has ended
    // onseeking        Script to be run when the seeking attribute is set to true indicating that seeking is active
    // onstalled        Script to be run when the browser is unable to fetch the media data for whatever reason
    // onsuspend        Script to be run when fetching the media data is stopped before it is completely loaded for whatever reason
    // ontimeupdate     Script to be run when the playing position has changed (like when the user fast forwards to a different point in the media)
    // onvolumechange   Script to be run each time the volume is changed which (includes setting the volume to "mute")
    // onwaiting        Script to be run when the media has paused but is expected to resume (like when the media pauses to buffer more data)
    // -----------
    // Misc Events
    // onerror         Fires when an error occurs while loading an external file
    // onshow          Fires when a <menu> element is shown as a context menu
    // ontoggle        Fires when the user opens or closes the <details> element
    public static function event_attributes(array $attr = []) : string
    {
        if (empty($attr)) {
            return '';
        }

        $html = '';
        if (isset($attr['onafterprint'])) { $html .= ' onafterprint="' . $attr['onafterprint'] . '"'; }
        if (isset($attr['onbeforeprint'])) { $html .= ' onbeforeprint="' . $attr['onbeforeprint'] . '"'; }
        if (isset($attr['onbeforeunload'])) { $html .= ' onbeforeunload="' . $attr['onbeforeunload'] . '"'; }
        if (isset($attr['onerror'])) { $html .= ' onerror="' . $attr['onerror'] . '"'; }
        if (isset($attr['onhashchange'])) { $html .= ' onhashchange="' . $attr['onhashchange'] . '"'; }
        if (isset($attr['onload'])) { $html .= ' onload="' . $attr['onload'] . '"'; }
        if (isset($attr['onmessage'])) { $html .= ' onmessage="' . $attr['onmessage'] . '"'; }
        if (isset($attr['onoffline'])) { $html .= ' onoffline="' . $attr['onoffline'] . '"'; }
        if (isset($attr['ononline'])) { $html .= ' ononline="' . $attr['ononline'] . '"'; }
        if (isset($attr['onpagehide'])) { $html .= ' onpagehide="' . $attr['onpagehide'] . '"'; }
        if (isset($attr['onpageshow'])) { $html .= ' onpageshow="' . $attr['onpageshow'] . '"'; }
        if (isset($attr['onpopstate'])) { $html .= ' onpopstate="' . $attr['onpopstate'] . '"'; }
        if (isset($attr['onresize'])) { $html .= ' onresize="' . $attr['onresize'] . '"'; }
        if (isset($attr['onstorage'])) { $html .= ' onstorage="' . $attr['onstorage'] . '"'; }
        if (isset($attr['onunload'])) { $html .= ' onunload="' . $attr['onunload'] . '"'; }
        if (isset($attr['onblur'])) { $html .= ' onblur	="' . $attr['onblur'] . '"'; }
        if (isset($attr['onchange'])) { $html .= ' onchange="' . $attr['onchange'] . '"'; }
        if (isset($attr['oncontextmenu'])) { $html .= ' oncontextmenu="' . $attr['oncontextmenu'] . '"'; }
        if (isset($attr['onfocus'])) { $html .= ' onfocus="' . $attr['onfocus'] . '"'; }
        if (isset($attr['oninput'])) { $html .= ' oninput="' . $attr['oninput'] . '"'; }
        if (isset($attr['oninvalid'])) { $html .= ' oninvalid="' . $attr['oninvalid'] . '"'; }
        if (isset($attr['onreset'])) { $html .= ' onreset="' . $attr['onreset'] . '"'; }
        if (isset($attr['onsearch'])) { $html .= ' onsearch="' . $attr['onsearch'] . '"'; }
        if (isset($attr['onselect'])) { $html .= ' onselect="' . $attr['onselect'] . '"'; }
        if (isset($attr['onsubmit'])) { $html .= ' onsubmit="' . $attr['onsubmit'] . '"'; }
        if (isset($attr['onkeydown'])) { $html .= ' onkeydown="' . $attr['onkeydown'] . '"'; }
        if (isset($attr['onkeypress'])) { $html .= ' onkeypress="' . $attr['onkeypress'] . '"'; }
        if (isset($attr['onkeyup'])) { $html .= ' onkeyup="' . $attr['onkeyup'] . '"'; }
        if (isset($attr['onclick'])) { $html .= ' onclick="' . $attr['onclick'] . '"'; }
        if (isset($attr['ondblclick'])) { $html .= ' ondblclick="' . $attr['ondblclick'] . '"'; }
        if (isset($attr['ondrag'])) { $html .= ' ondrag="' . $attr['ondrag'] . '"'; }
        if (isset($attr['ondragend'])) { $html .= ' ondragend="' . $attr['ondragend'] . '"'; }
        if (isset($attr['ondragenter'])) { $html .= ' ondragenter="' . $attr['ondragenter'] . '"'; }
        if (isset($attr['ondragleave'])) { $html .= ' ondragleave="' . $attr['ondragleave'] . '"'; }
        if (isset($attr['ondragover'])) { $html .= ' ondragover="' . $attr['ondragover'] . '"'; }
        if (isset($attr['ondragstart'])) { $html .= ' ondragstart="' . $attr['ondragstart'] . '"'; }
        if (isset($attr['ondrop'])) { $html .= ' ondrop="' . $attr['ondrop'] . '"'; }
        if (isset($attr['onmousedown'])) { $html .= ' onmousedown="' . $attr['onmousedown'] . '"'; }
        if (isset($attr['onmousemove'])) { $html .= ' onmousemove="' . $attr['onmousemove'] . '"'; }
        if (isset($attr['onmouseout'])) { $html .= ' onmouseout="' . $attr['onmouseout'] . '"'; }
        if (isset($attr['onmouseover'])) { $html .= ' onmouseover="' . $attr['onmouseover'] . '"'; }
        if (isset($attr['onmouseup'])) { $html .= ' onmouseup="' . $attr['onmouseup'] . '"'; }
        if (isset($attr['onmousewheel'])) { $html .= ' onmousewheel="' . $attr['onmousewheel'] . '"'; }
        if (isset($attr['onscroll'])) { $html .= ' onscroll="' . $attr['onscroll'] . '"'; }
        if (isset($attr['onwheel'])) { $html .= ' onwheel="' . $attr['onwheel'] . '"'; }
        if (isset($attr['oncopy'])) { $html .= ' oncopy="' . $attr['oncopy'] . '"'; }
        if (isset($attr['oncut'])) { $html .= ' oncut="' . $attr['oncut'] . '"'; }
        if (isset($attr['onpaste'])) { $html .= ' onpaste="' . $attr['onpaste'] . '"'; }
        if (isset($attr['onabort'])) { $html .= ' onabort="' . $attr['onabort'] . '"'; }
        if (isset($attr['oncanplay'])) { $html .= ' oncanplay="' . $attr['oncanplay'] . '"'; }
        if (isset($attr['oncanplaythrough'])) { $html .= ' oncanplaythrough="' . $attr['oncanplaythrough'] . '"'; }
        if (isset($attr['oncuechange'])) { $html .= ' oncuechange="' . $attr['oncuechange'] . '"'; }
        if (isset($attr['ondurationchange'])) { $html .= ' ondurationchange="' . $attr['ondurationchange'] . '"'; }
        if (isset($attr['onemptied'])) { $html .= ' onemptied="' . $attr['onemptied'] . '"'; }
        if (isset($attr['onended'])) { $html .= ' onended="' . $attr['onended'] . '"'; }
        if (isset($attr['onerror'])) { $html .= ' onerror="' . $attr['onerror'] . '"'; }
        if (isset($attr['onloadeddata'])) { $html .= ' onloadeddata="' . $attr['onloadeddata'] . '"'; }
        if (isset($attr['onloadedmetadata'])) { $html .= ' onloadedmetadata="' . $attr['onloadedmetadata'] . '"'; }
        if (isset($attr['onloadstart'])) { $html .= ' onloadstart="' . $attr['onloadstart'] . '"'; }
        if (isset($attr['onpause'])) { $html .= ' onpause="' . $attr['onpause'] . '"'; }
        if (isset($attr['onplay'])) { $html .= ' onplay="' . $attr['onplay'] . '"'; }
        if (isset($attr['onplaying'])) { $html .= ' onplaying="' . $attr['onplaying'] . '"'; }
        if (isset($attr['onprogress'])) { $html .= ' onprogress="' . $attr['onprogress'] . '"'; }
        if (isset($attr['onratechange'])) { $html .= ' onratechange="' . $attr['onratechange'] . '"'; }
        if (isset($attr['onseeked'])) { $html .= ' onseeked="' . $attr['onseeked'] . '"'; }
        if (isset($attr['onseeking'])) { $html .= ' onseeking="' . $attr['onseeking'] . '"'; }
        if (isset($attr['onstalled'])) { $html .= ' onstalled="' . $attr['onstalled'] . '"'; }
        if (isset($attr['onsuspend'])) { $html .= ' onsuspend="' . $attr['onsuspend'] . '"'; }
        if (isset($attr['ontimeupdate'])) { $html .= ' ontimeupdate="' . $attr['ontimeupdate'] . '"'; }
        if (isset($attr['onvolumechange'])) { $html .= ' onvolumechange="' . $attr['onvolumechange'] . '"'; }
        if (isset($attr['onwaiting'])) { $html .= ' onwaiting="' . $attr['onwaiting'] . '"'; }
        if (isset($attr['onerror'])) { $html .= ' onerror="' . $attr['onerror'] . '"'; }
        if (isset($attr['onshow'])) { $html .= ' onshow="' . $attr['onshow'] . '"'; }
        if (isset($attr['ontoggle'])) { $html .= ' ontoggle="' . $attr['ontoggle'] . '"'; }
        
        return $html;
    }
    
    public static function inline($content) : string
    {
        if (\is_array($content)) {
            $text = '';
            foreach ($content as $str) {
                $text .= self::inline($str);
            }
            return $text;
        } else {
            return $content;
        }
    }

    public static function element(
            string $name, array $attr = [], string $custom = '',
            $inner = '', bool $close = false) : string
    {
        $html  = self::open($name);
        $html .= self::global_attributes($attr);
        $html .= self::event_attributes($attr);
        $html .= $custom;
        $html .= self::close();
        $html .= self::inline($attr['innerHTML'] ?? $inner);
        if ($close) {
            $html .= self::close($name);
        }
        
        return $html;
    }
    
    public static function open(string $name = '') : string
    {
        return "<$name";
    }

    public static function close(string $name = '', string $closure = '>') : string
    {
        return empty($name) ? $closure : "</$name" . $closure;
    }
}
