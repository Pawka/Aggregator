<?php

/**
 * App_Search_Ranker
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Ranker {

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


    protected function normalize($scores, $smallIsBetter = false) {
        $vsmall = 0.00001; //Avoid division by 0.
    }


    abstract public function rank();
   
}

