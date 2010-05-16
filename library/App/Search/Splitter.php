<?php

/**
 * App_Search_Splitter
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 *
 * @todo Add filters support.
 */
abstract class App_Search_Splitter implements App_Search_Splitter_Interface {

    /**
     * List of added filters.
     *
     * @var array
     */
    protected $filters = array();

    public function  __construct($config = array()) {

    }

    /**
     * Returns class name as namespace. Could be used for dinamicaly creating
     * splitter objects.
     *
     * @return string
     */
    public static function getNamespace() {
        return get_class();
    }

    /**
     * Implodes list of tokens to a string with space separator.
     */
    public function implode($array) {
        return implode(' ', $array);
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
}

