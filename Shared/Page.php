<?php
/**
 * @author Alan T. Miller <alan@alanmiller.com>
 * @copyright Copyright (C) 2010, Alan T Miller, All Rights Reserved.
 *
 */
class Shared_Page
{
    private $html;
    private $body;
    private $body_id;
    private $body_class;
    private $title;
    private $title_suffix;
    private $title_seperator = ' :: ';
    private $meta_comment;
    private $meta_tags = array();
    private $stylesheets = array();
    private $javascripts = array();
    private $doctype = 'XHTML_1_0_STRICT';
    private $doctypes = array(
        'HTML_4_01_STRICT' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"\t
        \"http://www.w3.org/TR/html4/strict.dtd\">",
        'HTML_4_01_TRANSITIONAL' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\t
        \"http://www.w3.org/TR/html4/loose.dtd\">",
        'HTML_4_01_FRAMESET' => "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"\t
        \"http://www.w3.org/TR/html4/frameset.dtd\">",
        'XHTML_1_0_STRICT' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\t
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">",
        'XHTML_1_0_TRANSITIONAL' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\t
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">",
        'XHTML_1_0_FRAMESET' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"\t
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">",
        'XHTML_1_1' => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\t
        \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">"
        );


    public function __construct()
    {
        return $this;
    }

    public function setDoctype($str)
    {
        if (!in_array($str, array_keys($this->doctypes))) {
            throw new Exception('specified doctype not a valid option');
        }
        $this->doctype = $str;
        return $this;
    }

    public function setTitle($str)
    {
        $this->title = $str;
        return $this;
    }

    public function setTitleSuffix($suffix)
    {
        $this->title_suffix = $suffix;
        return $this;
    }

    public function setTitleSeperator($str)
    {
        $this->title_seperator = $str;
        return $this;
    }

    public function setBodyId($str)
    {
        $this->body_id = $str;
        return $this;
    }

    public function setBodyClass($str)
    {
        $this->body_class = $str;
        return $this;
    }

    public function setMetaData($key, $val)
    {
        $this->meta_tags[$key] = $val;
        return $this;
    }

    public function addStyleSheet($url, $media = 'all')
    {
        $this->stylesheets[] =
            array(
                'media' => $media,
                'url' => $url
                );
        return $this;
    }

    public function addJavascript($url)
    {
        $this->javascripts[] = $url;
        return $this;
    }

    public function setMetaComment($str)
    {
        $this->meta_comment = $str;
        return $this;
    }

    public function addBodyContent($str)
    {
        $this->body = $str;
        return $this;
    }

    public function clearStyleSheets()
    {
        $this->stylesheets = array();
        return $this;
    }

    public function clearJavascripts()
    {
        $this->javascripts = array();
        return $this;
    }

    public function clearMetaTags()
    {
        $this->meta_tags = array();
        return $this;
    }

    public function clearMetaData($key)
    {
        if (isset($this->meta_tags[$key])) {
            unset($this->meta_tags[$key]);
        }
        return $this;
    }

    public function display()
    {
        $this->_build();
        print($this->html);
        return $this;
    }

    public function fetch()
    {
        $this->_build();
        return $this->html;
    }

    public function toString()
    {
        $this->_build();
        return $this->html;
    }

    private function _append($html)
    {
        $this->html .= $html ."\n";
    }

    private function _build()
    {
        $this->_append($this->doctypes[$this->doctype]);
        $this->_append('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">');
        $this->_append('<head>');

        // add title tag
        if(strlen($this->title_suffix) > 0 && strlen($this->title) > 0) {
            $this->_append(sprintf('<title>%s</title>',$this->title .
                $this->title_seperator . $this->title_suffix));
        } elseif (empty($this->title_suffix) && strlen($this->title) > 0) {
            $this->_append(sprintf('<title>%s</title>', $this->title));
        } else {
            $this->_append(sprintf('<title>%s</title>', $_SERVER["SCRIPT_NAME"]));
        }

        // add meta tags
        if(count($this->meta_tags > 0)) {
            foreach($this->meta_tags AS $name => $content) {
                $this->_append(sprintf('<meta name="%s" content="%s" />', $name, $content));
            }
        }

        $this->_append('<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />');

        // add external stylesheet links
        if(count($this->stylesheets) > 0) {
            foreach($this->stylesheets AS $val) {
                $this->_append(sprintf('<style type="text/css" media="%s">@import "%s";</style>',
                    $val['media'], $val['url']));
            }
        }

        // add external javascript file links
        if(count($this->javascripts) > 0) {
            foreach($this->javascripts AS $url) {
                $this->_append(sprintf('<script type="text/javascript" src="%s"></script>', $url));
            }
        }

        // if defined add meta comment
        if(strlen($this->meta_comment) > 0) {
            $this->_append(wordwrap('<!-- '.$this->meta_comment.' -->', 120));
        }

        // close head section
        $this->_append('</head>');

        // add openning body tag
        if (strlen($this->body_class) > 0 && strlen($this->body_id) > 0) {
            $this->_append(sprintf('<body id="%s" class="%s">',$this->body_id, $this->body_class));
        } else if(strlen($this->body_id) > 0) {
            $this->_append(sprintf('<body id="%s">', $this->body_id));
        } else {
            $this->_append('<body>');
        }

        // add body
        if(strlen($this->body) > 0) {
            $this->_append($this->body);
        }

        // close out page
        $this->_append('<!-- created: '.date('l jS \of F Y h:i:s A').' -->');
        $this->_append('</body>');
        $this->_append('</html>');
    }
}
