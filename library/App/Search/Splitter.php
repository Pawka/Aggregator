<?php

/**
 * App_Search_Splitter
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Splitter implements App_Search_Splitter_Interface {


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
}

