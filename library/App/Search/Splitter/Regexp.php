<?php

//require_once 'App/Search/Splitter.php';

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
    private $regexp = null;

    
    public function __construct($config = array()) {

        if (array_key_exists('regexp', $config)) {
            $this->setRegexp($config['regexp']);
        }
        else {
            $regexp = "/([\s\-_:;?!\/\(\)\[\]{}<>\r\n\"'„“]|(?<!\d)[\.,](?!\d))/";
            $this->setRegexp($regexp);
        }
    }


    /**
     * Set a new regexp to split content.
     *
     * @param string $regexp
     */
    public function setRegexp($regexp) {
        $this->regexp = $regexp;
    }

    /**
     * Return regexp pattern.
     * 
     * @return string
     */
    public function getRegexp() {
        return $this->regexp;
    }

    /**
     * Splits string to tokens.
     *
     * @param string $content
     * @return array
     */
    public function split($content) {
        $result = preg_split($this->getRegexp(), $content, null, PREG_SPLIT_NO_EMPTY);
        $result = $this->runFilters($result);
        return $result;
    }
}

