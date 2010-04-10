<?php

class Admin_InterfaceController extends Site_Controller_Admin
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


    /**
     * List of elements with paginator and sorting features.
     * @var Zend_Db_Select $select
     */
    public function listAction() {
        if ($this->_hasParam('select') == false) {
            throw new Exception('List action must have Zend_Db_Select object as "select" param.', 1001);
        }


        $select = $this->_getParam('select');
        $page   = $this->_getParam('page', 1);
        $by     = $this->_getParam('by', 'id');
        $order  = $this->_getParam('order', 'ASC');
        $cols   = $this->_getParam('cols', array('id'));

        $select->order("{$by} {$order}");
        
        $paginator = new Site_Paginator($this->view);
        $this->view->paginator = $paginator->init($select, $page);
        $this->view->cols = $cols;
        $this->view->controllerUrl = $this->getControllerUrl($this->_getParam('controller'), false);
        $this->view->order = $order;
        $this->view->by = $by;
    }
}

