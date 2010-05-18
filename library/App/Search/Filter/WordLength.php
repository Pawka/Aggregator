<?php

/**
 * App_Search_Filter_WordLength
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Filter_WordLength extends App_Search_Filter {

    /**
     * Splitter object to explode text to tokens.
     *
     * @var App_Search_Splitter
     */
    private $_splitter = null;

    /**
     * Min words length value to pass.
     *
     * @var int
     */
    private $_min = 0;

    /**
     * Max words length value to pass.
     *
     * @var int
     */
    private $_max = null;


    private $_charset = 'utf8';

    public function  __construct($config = array()) {

        if (array_key_exists('min', $config)) {
            $this->setMin($config['min']);
        }

        if (array_key_exists('max', $config)) {
            $this->setMax($config['max']);
        }

        if (array_key_exists('splitter', $config)) {
            $this->setSplitter($config['splitter']);
        }
    }

    public function setMin($value) {
        $this->_min = $value;
        return $this;
    }

    public function getMin() {
        return $this->_min;
    }

    public function setMax($value) {
        $this->_max = $value;
        return $this;
    }

    public function getMax() {
        return $this->_max;
    }

    /**
     * Set splitter
     *
     * @param App_Search_Splitter $splitter
     * @return App_Search_Indexer_Abstract
     */
    public function setSplitter($splitter) {
        if (is_string($splitter)) {
            $class_name = App_Search_Splitter::getNamespace() . '_' . ucfirst($splitter);
            if (class_exists($class_name)) {
                $this->_splitter = new $class_name();
            }
            else {
                throw new Exception('Unknown splitter class: ' . $class_name);
            }
        }
        else {
            $this->_splitter = $splitter;
        }
        return $this;
    }


    /**
     * Return logger object.
     *
     * @return Zend_Logger
     */
    public function getSplitter() {
        return $this->_splitter;
    }


    /**
     * Filter words between min and max numbers of letters.
     * 
     * @param string $content
     * @return string
     */
    public function run($content) {

        if ($this->getMin() <= 0 && $this->getMax() == null) {
            return $content;
        }

        $words = $content;
        if (!is_array($content)) {
            $words = array($content);
            $splitter = $this->getSplitter();
            if ($splitter !== null) {
                $words = $splitter->split($content);
            }
        }

        if (is_array($words)) {
            $result = array();
            foreach ($words as $word) {
                if ($this->isValid($word)) {
                    $result[] = $word;
                }
            }
            return $result;
        }

        return $this->isValid($content) === true ? $content : null;
    }


    /**
     * Checks if word length is valid with min and max values.
     * @param string $word
     * @return boolean
     */
    private function isValid($word) {
        if (mb_strlen($word, $this->_charset) < $this->_min) {
            return false;
        }

        if ($this->_max !== null && mb_strlen($word, $this->_charset) > $this->_max) {
            return false;
        }

        return true;
    }


}
?>
