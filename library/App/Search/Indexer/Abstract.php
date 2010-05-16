<?php

/**
 * App_Search_Indexer_Abstract
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Indexer_Abstract extends App_Search_Base {

    /**
     * Filter type used before spliting.
     */
    const FILTER_POST = 1;

    /**
     * Filter type used after spliting.
     */
    const FILTER_PRE = 2;


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
     * List of added filters.
     *
     * @var array
     */
    protected $_preFilters = array();


    /**
     * List of added postfilters.
     *
     * @var array
     */
    protected $_postFilters = array();

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
     * @param App_Search_Filter $filter
     * @param array $params
     * @param int $type
     * @return App_Search_Indexer_Abstract
     */
    public function setFilter($filter, $params = array(), $type = self::FILTER_PRE) {
        if (is_string($filter)) {
            $class_name = App_Search_Filter::getNamespace() . '_' . ucfirst($filter);
            if (class_exists($class_name)) {
                $this->_addFilterObject(new $class_name($params), $type);
            }
            else {
                throw new Exception('Unknown filter class: ' . $class_name);
            }
        }
        else {
            $this->_addFilterObject($filter, $type);
        }
        return $this;
    }


    /**
     * Adds filter object to the list depending of type.
     */
    private function _addFilterObject($filter, $type) {
        switch ($type) {
            case self::FILTER_POST:
                $this->_postFilters[] = $filter;
                break;

            case self::FILTER_PRE:
                $this->_preFilters[] = $filter;
                break;
        }
    }


    /**
     * Returns list of filters depending of type.
     * @param int $type
     * @return array
     */
    public function getFilters($type = self::FILTER_PRE) {
        switch ($type) {
            case self::FILTER_POST:
                return $this->_postFilters;
                break;

            case self::FILTER_PRE:
                return $this->_preFilters;
                break;
        }

        return null;
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
    protected function runFilters($content, $type = self::FILTER_PRE) {
        
        $filters = $this->getFilters($type);
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $content = $filter->run($content);
            }
        }
        return $content;
    }

}
?>
