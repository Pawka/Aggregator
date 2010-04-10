<?php

class App_Model_Page
{

    public function getRandomHtmlColor()
    {
        $red = rand(0,255);
        $green = rand(0,255);
        $blue = rand(0,255);
        return $this->getHtmlColor($red, $green, $blue);
    }

    public function getHtmlColor($red, $green, $blue)
    {
        $color = '#' . $this->_getHex($red)
                     . $this->_getHex($green)
                     . $this->_getHex($blue);
        return $color;
    }

    protected function _getHex($number, $digits = 2)
    {
        return substr(str_repeat('0', $digits) .
                         dechex($number), - $digits);
    }

}