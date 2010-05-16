<?php

/**
 * App_Search_Indexer_Abstract
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Indexer_Abstract extends App_Search_Base {

    /**
     * @var Zend_Log
     */
    protected $logger;


    /**
     * Splitter object
     *
     * @var App_Search_Splitter
     */
    protected $splitter;


    /**
     * List of used filters.
     *
     * @var array
     */
    protected $filters = array();


    public function  __construct($config = array()) {

        if (array_key_exists('logger', $config)) {
            $this->setLogger($config['logger']);
        }
        else {
            $this->setLogger(Zend_Registry::get('logger'));
        }


        if (array_key_exists('splitter', $config)) {
            $this->setSplitter($config['splitter']);
        }
        else {
            $this->setSplitter('Regexp');
        }
    }


    /**
     * Adds filter to filters list.
     */
    public function setFilter($filter, $params = array()) {
        if (is_string($filter)) {
            $class_name = App_Search_Filter::getNamespace() . '_' . ucfirst($filter);
            if (class_exists($class_name)) {
                $this->filters[] = new $class_name($params);
            }
            else {
                throw new Exception('Unknown filter class: ' . $class_name);
            }
        }
        else {
            $this->filters[] = $filter;
        }
        return $this;
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
                $this->splitter = new $class_name();
            }
            else {
                throw new Exception('Unknown splitter class: ' . $class_name);
            }
        }
        else {
            $this->splitter = $splitter;
        }
        return $this;
    }


    /**
     * Return logger object.
     *
     * @return Zend_Logger
     */
    public function getSplitter() {
        return $this->splitter;
    }



    /**
     * Set looger
     * 
     * @param Zend_Logger $logger
     * @return App_Search_Indexer_Abstract
     */
    public function setLogger($logger) {
        $this->logger = $logger;
        return $this;
    }


    /**
     * Return logger object.
     *
     * @return Zend_Logger
     */
    public function getLogger() {
        return $this->logger;
    }


    /**
     * Applies setted filters for given content.
     * @param string $content
     * @return string
     */
    protected function runFilters($content) {

        foreach ($this->filters as $filter) {
            $content = $filter->run($content);
        }

        return $content;
    }

}
?>
