<?php
/**
 * @author Alan T. Miller <alan@alanmiller.com>
 * @copyright Copyright (C) 2023, Alan T Miller, All Rights Reserved.
 *
 * Class Shared_Page.
 *
 * This class provides an object-oriented way to generate HTML pages.
 */
class Shared_Page
{
    private string $html;
    private string $body;
    private string $body_id;
    private string $body_class;
    private string $title;
    private string $title_suffix;
    private string $title_separator = ' :: ';
    private string $meta_comment;
    private string $author;

    private array $meta_tags = [];
    private array $stylesheets = [];
    private array $head_javascripts = [];
    private array $footer_javascripts = [];
    private string $doctype = "<!doctype html>";
    private string $viewport = "width=device-width, initial-scale=1";

    public function __construct()
    {
        return $this;
    }

    /**
     * Set the title of the page.
     *
     * @param string $str
     * @return $this
     */
    public function setTitle(string $str): self
    {
        $this->title = $str;
        return $this;
    }

    /**
     * Set the suffix of the page title.
     *
     * @param string $suffix
     * @return $this
     */
    public function setTitleSuffix(string $suffix): self
    {
        $this->title_suffix = $suffix;
        return $this;
    }

    /**
     * Set the separator character(s) for the page title.
     *
     * @param string $str
     * @return $this
     */
    public function setTitleSeperator(string $str): self
    {
        $this->title_seperator = $str;
        return $this;
    }

    /**
     * Set the id for the body tag.
     *
     * @param string $str
     * @return $this
     */
    public function setBodyId(string $str): self
    {
        $this->body_id = $str;
        return $this;
    }

    /**
     * Set the class for the body tag.
     *
     * @param string $str
     * @return $this
     */
    public function setBodyClass(string $str): self
    {
        $this->body_class = $str;
        return $this;
    }

    /**
     * Add or update a meta tag.
     *
     * @param string $key
     * @param string $val
     * @return $this
     */
    public function setMetaData(string $key, string $val): self
    {
        $this->meta_tags[$key] = $val;
        return $this;
    }

    /**
     * Add a CSS stylesheet.
     *
     * @param string $url
     * @param string $media
     * @return $this
     */
    public function addStyleSheet(string $url, string $media = 'all'): self
    {
        $this->stylesheets[] = [
            'media' => $media,
            'url' => $url
        ];
        return $this;
    }

    public function addJavascript(string $url, string $position = 'footer'): self
    {
        if ($position == 'header') {
            $this->head_javascripts[] = $url;
        } else {
            $this->footer_javascripts[] = $url;
        }

        return $this;
    }

    /**
     * Set a comment that will be placed in the meta tags section.
     *
     * @param string $str
     * @return $this
     */
    public function setMetaComment(string $str): self
    {
        $this->meta_comment = $str;
        return $this;
    }

    /**
     * Add content to the body of the page.
     *
     * @param string $str
     * @return $this
     */
    public function addBodyContent(string $str): self
    {
        $this->body .= $str;
        return $this;
    }

    public function clearStyleSheets(): self
    {
        $this->stylesheets = [];
        return $this;
    }

    public function clearJavascripts(): self
    {
        $this->head_javascripts = [];
        $this->footer_javascripts = [];
        return $this;
    }

    public function clearMetaTags(): self
    {
        $this->meta_tags = [];
        return $this;
    }

    public function clearMetaData(string $key): self
    {
        unset($this->meta_tags[$key]);
        return $this;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function display(): self
    {
        $this->build();
        print($this->html);
        return $this;
    }

    public function fetch(): string
    {
        $this->build();
        return $this->html;
    }

    public function toString(): string
    {
        $this->build();
        return $this->html;
    }

    private function append(string $html): self
    {
        $this->html .= $html . "\n";
        return $this;
    }

    private function build(): self
    {
        // Set up HTML skeleton
        $this->append($this->doctype);
        $this->append('<html lang="en">');
        $this->append('<head>');
        $this->append('<meta charset="UTF-8">');
        $this->append('<meta name="viewport" content="' . $this->viewport . '">');

        // If author is set, append author meta tag
        if ($this->author) {
            $this->append('<meta name="author" content="' . $this->author . '">');
        }

        // If meta comment is set, append meta comment
        if ($this->meta_comment) {
            $this->append('<!-- ' . $this->meta_comment . ' -->');
        }

        // Add JavaScript files to head if any
        if (!empty($this->javascripts_head)) {
            foreach ($this->javascripts_head as $javascript) {
                $this->html .= "<script src=\"$javascript\"></script>\n";
            }
        }

        // Title
        $fullTitle = $this->title;
        if ($this->title_suffix) {
            $fullTitle .= $this->title_separator . $this->title_suffix;
        }
        $this->append('<title>' . ($fullTitle ?? $_SERVER["SCRIPT_NAME"]) . '</title>');


        // Add meta tags
        if (!empty($this->meta_tags)) {
            foreach ($this->meta_tags as $name => $content) {
                $this->html .= "<meta name=\"$name\" content=\"$content\">\n";
            }
        }

        // Add stylesheets
        if (!empty($this->stylesheets)) {
            foreach ($this->stylesheets as $stylesheet) {
                $this->html .= "<link rel=\"stylesheet\" href=\"$stylesheet\">\n";
            }
        }
        $this->append('</head>');

        // Body tag with optional id and class attributes
        $bodyAttr = [];
        if ($this->body_id) {
            $bodyAttr[] = 'id="' . $this->body_id . '"';
        }
        if ($this->body_class) {
            $bodyAttr[] = 'class="' . $this->body_class . '"';
        }
        $this->append('<body' . (empty($bodyAttr) ? '' : ' ' . implode(' ', $bodyAttr)) . '>');

        // Body content
        $this->append($this->body ?? '');

        // Add JavaScript files to body if any
        if (!empty($this->javascripts_body)) {
            foreach ($this->javascripts_body as $javascript) {
                $this->html .= "<script src=\"$javascript\"></script>\n";
            }
        }

        $this->append('<!-- Created: ' . date('l jS \of F Y h:i:s A') . ' -->');
        $this->append('</body>');
        $this->append('</html>');

        return $this;
    }
}
