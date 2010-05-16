<?php

/**
 * App_Search_Filter_CleanHTML
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Filter_CleanHTML extends App_Search_Filter {


    /**
     * Removes HTML tags from content.
     * 
     * @param string $content
     * @return string
     */
    public function run($content) {
        return strip_tags($content);
    }
    

}
?>
