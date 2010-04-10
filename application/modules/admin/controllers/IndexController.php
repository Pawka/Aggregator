<?php

class Admin_IndexController extends Site_Controller_Admin
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $admins = new App_Model_Table_Admins();

    }


}

