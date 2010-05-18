<?php

/**
 * App_Search_Filter_Lowercase
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Filter_Lowercase extends App_Search_Filter {


    /**
     * Converts content to lowercase string.
     * 
     * @param string $content
     * @return string
     */
    public function run($content) {
        return strtolower($content);
    }
    

}
?>
