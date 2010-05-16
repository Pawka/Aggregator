<?php

/**
 * App_Search_Splitter
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Splitter implements App_Search_Splitter_Interface {

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

