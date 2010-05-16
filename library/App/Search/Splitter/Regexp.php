<?php

/**
 * Splitter
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Splitter_Regexp extends App_Search_Splitter implements App_Search_Splitter_Interface {


    /**
     * Regexp pattern used to split content
     * @var string
     */
    private $pattern = null;

    public function  __construct() {
        $pattern = "/([\s\-_:;?!\/\(\)\[\]{}<>\r\n\"'„“]|(?<!\d)[\.,](?!\d))/";
        $this->setPattern($pattern);
    }


    /**
     * Set a new regexp to split content.
     * @param string $regexp
     */
    public function setPattern($regexp) {
        $this->pattern = $regexp;
    }


    public function getPattern() {
        return $this->pattern;
    }

    public function split($content) {
        
    }
}

