<?php

/**
 * App_Search_Filter_Stopwords
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Filter_Stopwords extends App_Search_Filter {


    /**
     * List of stopwords should be removed from content.
     *
     * @var array
     */
    private $_stopwords = array();

    /**
     * Splitter object to explode text to tokens.
     *
     * @var App_Search_Splitter
     */
    private $_splitter = null;

    /**
     * Cache backend
     *
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    protected $_cache_key = 'filter_stopwords';


    public function  __construct($config = array()) {

        if (array_key_exists('cache', $config)) {
            $this->setCache($config['cache']);
        }

        if (array_key_exists('stopwords', $config)) {
            $this->setStopwords($config['stopwords']);
        }

        if (array_key_exists('splitter', $config)) {
            $this->setSplitter($config['splitter']);
        }
    }

    /**
     * @param Zend_Cache_Core $cache
     */
    public function setCache($cache) {
        $this->_cache = $cache;
    }

    /**
     * Adds stopwords to cache
     */
    private function _addToCache() {
        if ($this->_cache !== null) {
            if (!empty($this->_stopwords)) {
                foreach ($this->_stopwords as $word) {
                    $index = $this->_makeTokenKey($word);
                    $this->_cache->save(1, $index);
                }
            }
        }
    }

    
    /**
     * Makes token key for saving in cache.
     * @param string $word
     * @return string
     */
    private function _makeTokenKey($word) {
        return $this->_cache_key . '_' . md5($word);
    }


    /**
     * Sets stopwords list to object.
     *
     * @param Zend_Config|array $list
     * @return App_Search_Filter_Stopwords
     */
    public function setStopwords($list) {
        if ($list instanceof Zend_Config) {
            $this->_stopwords = $list->stopwords->item->toArray();
        }
        elseif (is_array($list)) {
            $this->_stopwords = $list;
        }
        Zend_Registry::get('logger')->info("Total stopwords: " . count($this->_stopwords));
        $this->_addToCache();

        return $this;
    }


    /**
     * Returns list of stopwords.
     * @return array
     */
    public function getStopwords() {
        return $this->_stopwords;
    }


    /**
     * Adds a word to stopwords list.
     *
     * @param string $word
     * @return App_Search_Filter_Stopwords
     */
    public function addStopword($word) {
        if (is_string($word)) {
            $this->_stopwords[] = $word;
        }
        return $this;
    }

    
    /**
     * Clears stopwords list.
     * @return App_Search_Filter_Stopwords
     */
    public function clearStopwords() {
        $this->setStopwords(array());
        return $this;
    }


    /**
     * Returns numer of items in stopwords list.
     *
     * @return int
     */
    public function getCount() {
        return count($this->getStopwords());
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
     * Filter stopwords from content.
     * 
     * @param string $content
     * @return string
     */
    public function run($content) {
        if ($this->getCount() == 0) {
            return $content;
        }

        $words = $content;
        if (!is_array($content)) {
            $splitter = $this->getSplitter();
            if ($splitter !== null) {
                $words = $splitter->split($content);
            }
        }

        if (is_array($words)) {
            $result = array();
            foreach ($words as $word) {
                if ($this->exists($word) === false) {
                    $result[] = $word;
                }
            }
            return $result;
        }

        return $this->exists($content) === true ? null : $content;
    }


    /**
     * Checks if given word exists in stopwords list.
     * @param string $word
     * @return boolean
     */
    public function exists($word) {
        if ($this->_cache === null) {
            return (in_array($word, $this->getStopwords()));
        }
        else {
            $index = $this->_makeTokenKey($word);
            $result = (boolean)$this->_cache->load($index);
            return $result;
        }
    }
}
?>
