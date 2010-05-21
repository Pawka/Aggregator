<?php

/**
 * App_Search_Filter
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Filter extends App_Search_Base implements App_Search_Filter_Interface {

    public function  __construct($config = array()) {
        $this->setOptions($config);
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
   
}

