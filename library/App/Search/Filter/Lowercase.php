<?php

/**
 * App_Search_Filter_Lowercase
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Filter_Lowercase extends App_Search_Filter {


    protected $_charset = 'utf8';

    /**
     * Converts content to lowercase string.
     * 
     * @param string $content
     * @return string
     */
    public function run($content) {
        if (is_array($content)) {
            foreach ($content as $key => $row) {
                $content[$key] = $this->_toLower($row);
            }
        }
        else {
            $content = $this->_toLower($content);
        }
        return $content;
    }

    private function _toLower($content) {
        return mb_strtolower($content, $this->_charset);
    }

}
?>
