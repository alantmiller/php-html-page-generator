<?php
/**
 * @author Alan T. Miller <alan@alanmiller.com>
 * @copyright Copyright (C) 2023, Alan T Miller, All Rights Reserved.
 *
 * The class provides an object-oriented way to generate HTML pages.
 * You can configure various aspects of the page, like its title, body ID, body content,
 * and then use the `display` method to send the page to the client.
 *
 * Here is an example of how to use the Shared_Page class.
 *
 * Create a new page.
 * $page = new Shared_Page();
 *
 * Set various properties on the page.
 *
 * $page->setTitle('My Sample Page')
 *     ->setTitleSuffix(' - My Website')
 *     ->setBodyId('sample-page')
 *     ->setBodyClass('my-style')
 *     ->setAuthor('Your Name')
 *     ->setMetaData('description', 'This is a sample page created using Shared_Page class.')
 *     ->setMetaData('keywords', 'Sample, Shared_Page, PHP')
 *     ->setOpenGraphData('og:title', 'My Sample Page')
 *     ->setOpenGraphData('og:description', 'This is a sample page created using Shared_Page class.')
 *     ->setOpenGraphData('og:url', 'https://www.example.com/sample-page')
 *     ->setOpenGraphData('og:image', 'https://www.example.com/images/sample-page.jpg')
 *     ->setTwitterCardData('twitter:card', 'summary')
 *     ->setTwitterCardData('twitter:site', '@yourTwitterHandle')
 *     ->addBodyContent('<h1>Welcome to My Sample Page</h1><p>This is a sample page.</p>')
 *     ->addStyleSheet('https://www.example.com/css/styles.css')
 *     ->addJavascript('https://www.example.com/js/script.js');
 *
 * Send caching headers and display the page.
 *
 * $page->setCacheHeaders('public, max-age=86400', 86400)
 *     ->display();
 *
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
    private array $openGraphData = [];
    private array $twitterCardData = [];
    private string $doctype = "<!doctype html>";
    private string $viewport = "width=device-width, initial-scale=1";

    public function __construct()
    {
        return $this;
    }

    /**
     * Set caching headers.
     *
     * @param string $cacheControl
     * @param string $expires
     * @return $this
     */
    public function setCacheHeaders(string $cacheControl = 'no-cache', int $expires = 0): self
    {
        // Check if headers have been sent
        if (headers_sent()) {
            error_log('Cannot set headers, headers already sent');
            return $this;
        }

        header('Cache-Control: ' . $cacheControl);

        if ($expires > 0) {
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
        }

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
     * Set an Open Graph property.
     *
     * @param string $property
     * @param string $content
     * @return $this
     */
    public function setOpenGraphData(string $property, string $content): self
    {
        $this->openGraphData[$property] = $content;
        return $this;
    }

    /**
     * Set a Twitter Card property.
     *
     * @param string $property
     * @param string $content
     * @return $this
     */
    public function setTwitterCardData(string $property, string $content): self
    {
        $this->twitterCardData[$property] = $content;
        return $this;
    }

    /**
     * Add a CSS stylesheet.
     *
     * @param string $url
     * @param string $media
     * @param bool $async
     * @return $this
     */
    public function addStyleSheet(string $url, string $media = 'all', bool $async = false): self
    {
        $this->stylesheets[] = [
            'media' => $media,
            'url' => $url,
            'async' => $async
        ];
        return $this;
    }

    /*
     * Adds a JavaScript file to the page.
     *
     * @param string $url The URL of the JavaScript file.
     * @param string $position The position of the JavaScript file (header or footer). Default is 'footer'.
     * @return $this Returns the Shared_Page object for method chaining.
     */
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


        // Add meta tags if any
        if (!empty($this->meta_tags)) {
            foreach ($this->meta_tags as $name => $content) {
                $this->html .= "<meta name=\"$name\" content=\"$content\">\n";
            }
        }

        // Process Open Graph data if the array has values
        if (!empty($this->openGraphData)) {
            foreach ($this->openGraphData as $property => $content) {
                $this->append('<meta property="og:' . $property . '" content="' . $content . '">');
            }
        }

        // Process Twitter Card data if the array has values
        if (!empty($this->twitterCardData)) {
            foreach ($this->twitterCardData as $property => $content) {
                $this->append('<meta name="twitter:' . $property . '" content="' . $content . '">');
            }
        }

        // Add stylesheets if any
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
