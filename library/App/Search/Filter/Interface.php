<?php

/**
 * App_Search_Filter_Interface
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
interface App_Search_Filter_Interface {

    /**
     * Constructor
     *
     * Accept options during initialization.
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null);

    /**
     * Run an action of current filter.
     *
     * @param string $content
     * @return string Filtered content.
     */
    public function run($content);

}

