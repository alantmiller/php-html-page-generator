<?php

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

    public function setTitle(string $str): self
    {
        $this->title = $str;
        return $this;
    }

    public function setTitleSuffix(string $suffix): self
    {
        $this->title_suffix = $suffix;
        return $this;
    }

    public function setTitleSeparator(string $str): self
    {
        $this->title_separator = $str;
        return $this;
    }

    public function setBodyId(string $str): self
    {
        $this->body_id = $str;
        return $this;
    }

    public function setBodyClass(string $str): self
    {
        $this->body_class = $str;
        return $this;
    }

    public function setMetaData(string $key, string $val): self
    {
        $this->meta_tags[$key] = $val;
        return $this;
    }

    public function addStyleSheet(string $url, string $media = 'all'): self
    {
        $this->stylesheets[] = ['media' => $media, 'url' => $url];
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

    public function setMetaComment(string $str): self
    {
        $this->meta_comment = $str;
        return $this;
    }

    public function addBodyContent(string $str): self
    {
        $this->body = $str;
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
        $this->append('<body' . ($this->body_id ? ' id="' . $this->body_id . '"' : '') . ($this->body_class ? ' class="' . $this->body_class . '"' : '') . '>');

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
