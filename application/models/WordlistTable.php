<?php

/**
 * App_Model_WordlistTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class App_Model_WordlistTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object App_Model_WordlistTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('App_Model_Wordlist');
    }
}