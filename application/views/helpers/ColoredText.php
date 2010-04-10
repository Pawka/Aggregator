<?php

class App_View_Helper_ColoredText extends Zend_View_Helper_Abstract
{

    public function coloredText($text, $color)
    {
        $text = $this->view->escape($text);
        $ctext = '<span style="color: ' . $color . '">' .
                     $text . '</span>';
        return $ctext;
    }

}