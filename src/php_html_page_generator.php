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
 * * Create a new page.
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
 *     ->addStyleSheet('https://www.example.com/css/styles.css', 'all', true)
 *     ->addJavascript('https://www.example.com/js/script.js', 'footer', true);
 *
 * Send caching headers and display the page.
 *
 * $page->setCacheHeaders('public, max-age=86400', 86400)
 *     ->display();
 *
 */
class PhpHtmlPageGenerator
{
    protected string $html;
    protected string $body;
    protected string $body_id;
    protected string $body_class;
    protected string $title;
    protected string $title_suffix;
    protected string $title_separator = ' :: ';
    protected string $meta_comment;
    protected string $author;
    protected array $meta_tags = [];
    protected array $stylesheets = [];
    protected array $head_javascripts = [];
    protected array $footer_javascripts = [];
    protected array $openGraphData = [];
    protected array $twitterCardData = [];
    protected string $doctype = "<!doctype html>";
    protected string $viewport = "width=device-width, initial-scale=1";

    public function __construct()
    {
        $this->setCharset(); // Defaults to UTF-8
        return $this;
    }

    /**
     * Set caching headers.
     *
     * @param string $cacheControl
     * @param string $expires
     * @return $this
     */
    protected function setCacheHeaders(string $cacheControl = 'no-cache', int $expires = 0): self
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
     * Set character set meta tag.
     *
     * @param string $charset The character set.
     * @return $this
     */
    protected function setCharset(string $charset = 'UTF-8'): self
    {
        $this->append('<meta charset="' . $charset . '">');
        return $this;
    }

    /**
     * Set the title of the page.
     *
     * @param string $str
     * @return $this
     */
    protected function setTitle(string $str): self
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
    protected function setTitleSuffix(string $suffix): self
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
    protected function setTitleSeparator(string $str): self
    {
        $this->title_separator = $str;
        return $this;
    }

    /**
     * Set the id for the body tag.
     *
     * @param string $str
     * @return $this
     */
    protected function setBodyId(string $str): self
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
    protected function setBodyClass(string $str): self
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
    protected function setMetaData(string $key, string $val): self
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
    protected function setOpenGraphData(string $property, string $content): self
    {
        if ($property == 'og:image') {
            $content = str_replace(array('http://', 'https://'), '//', $content);
        }

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
    protected function setTwitterCardData(string $property, string $content): self
    {
        if ($property == 'twitter:image') {
            $content = str_replace(array('http://', 'https://'), '//', $content);
        }

        $this->twitterCardData[$property] = $content;
        return $this;
    }

    /**
     * Set the favicon URL.
     *
     * @param string $url The URL to the favicon image.
     * @return $this
     */
    protected function setFavicon(string $url): self
    {
        $url = str_replace(array('http://', 'https://'), '//', $url);

        $this->append('<link rel="icon" href="' . $url . '">');

        return $this;
    }

    // Set canonical URL
    protected function setCanonicalUrl(string $url): self
    {
        $url = str_replace(array('http://', 'https://'), '//', $url);

        $this->append('<link rel="canonical" href="' . $url . '">');

        return $this;
    }

    /**
     * Add a CSS stylesheet.
     *
     * @param string $url The URL of the stylesheet.
     * @param string $media The media attribute for the stylesheet. Default is 'all'.
     * @param bool $preload Whether to preload the stylesheet. Default is false.
     * @return $this Returns the Shared_Page object for method chaining.
     */
    protected function addStyleSheet(string $url, string $media = 'all', bool $preload = false): self
    {
        // Change passed in URL to be protocol relative
        $url = str_replace(array('http://', 'https://'), '//', $url);

        $this->stylesheets[] = [
            'media' => $media,
            'url' => $url,
            'preload' => $preload
        ];
        return $this;
    }

    /**
     * Add a JavaScript file.
     *
     * @param string $url The URL of the JavaScript file.
     * @param string $position The position of the JavaScript file (header or footer). Default is 'footer'.
     * @param bool $async Whether to load the script asynchronously. Default is false.
     * @return $this Returns the Shared_Page object for method chaining.
     */
    protected function addJavascript(string $url, string $position = 'footer', bool $async = false): self
    {
        // Change passed in URL to be protocol relative
        $url = str_replace(array('http://', 'https://'), '//', $url);


        if ($position == 'header') {
            $this->head_javascripts[] = [
                'url' => $url,
                'async' => $async
            ];
        } else {
            $this->footer_javascripts[] = [
                'url' => $url,
                'async' => $async
            ];
        }

        return $this;
    }


    /**
     * Set a comment that will be placed in the meta tags section.
     *
     * @param string $str
     * @return $this
     */
    protected function setMetaComment(string $str): self
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
    protected function addBodyContent(string $str): self
    {
        $this->body .= $str;
        return $this;
    }

    protected function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    protected function display(): self
    {
        $this->build();
        print($this->html);
        return $this;
    }

    protected function fetch(): string
    {
        $this->build();
        return $this->html;
    }

    protected function toString(): string
    {
        $this->build();
        return $this->html;
    }

    protected function append(string $html): self
    {
        $this->html .= $html . "\n";
        return $this;
    }

    protected function build(): self
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
        if (!empty($this->head_javascripts)) {
            foreach ($this->head_javascripts as $javascript) {
                $asyncAttribute = $javascript['async'] ? 'async' : ''; // Check if async should be added
                $this->append('<script src="' . $javascript['url'] . '" ' . $asyncAttribute . '></script>');
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

        // Add stylesheets if any
        if (!empty($this->stylesheets)) {
            foreach ($this->stylesheets as $stylesheet) {
                $preloadAttribute = $stylesheet['preload'] ? 'preload' : ''; // Check if preload should be added
                $this->append('<link rel="stylesheet" href="' . $stylesheet['url'] . '" media="' . $stylesheet['media'] . '" ' . $preloadAttribute . '>');
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

        // If meta comment is set, append meta comment
        if ($this->meta_comment) {
            $this->append('<!-- ' . $this->meta_comment . ' -->');
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
        if (!empty($this->footer_javascripts)) {
            foreach ($this->footer_javascripts as $javascript) {
                $asyncAttribute = $javascript['async'] ? 'async' : ''; // Check if async should be added
                $this->append('<script src="' . $javascript['url'] . '" ' . $asyncAttribute . '></script>');
            }
        }

        $this->append('<!-- Created: ' . date('l jS \of F Y h:i:s A') . ' -->');
        $this->append('</body>');
        $this->append('</html>');

        return $this;
    }
}
