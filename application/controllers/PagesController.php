<?php

class PagesController extends Site_Controller_Pages
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        /**
         * @var Zend_Db_Table
         */
        $select = $this->db->select();
        $select = new Zend_Db_Table();
        //$select->
    }


}

