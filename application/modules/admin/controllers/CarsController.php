<?php

class Admin_CarsController extends Site_Controller_Admin {

    public function preDispatch() {
        parent::preDispatch();
        $this->view->headScript()->appendFile('../js/admin/jquery.cars.js', 'text/javascript');
    }

    public function indexAction() {
        $model = new App_Model_Table_Auto();

        $page       = $this->_getParam('page', 1);
        $sort       = $this->_getParam('sort', 'id');
        $order      = $this->_getParam('order', 'desc');



        $select = new Zend_Db_Select($this->db);
        $data = $select->order("{$sort} {$order}")
            ->from(array('a' => "auto"))
            ->joinLeft(array('m' => 'auto_makers'),
            'a.maker = m.id',
            array('maker_name'))
            ->joinLeft(array('md' => 'auto_models'),
            'a.model = md.id',
            array('model_name'))
            ->order("{$sort} {$order}");
        $paginator = new Site_Paginator($this->view);
        $this->view->paginator = $paginator->createPaginator($data, $page);

    }


    public function createAction() {
        $form = new Site_Form_Car();
        $this->setAjaxUrl();
        if ($this->getRequest()->isPost()) {
            if ($this->_postBack($form) == true) {
                $this->_helper->Redirector->gotoUrl('admin/cars/');
            }
        }

        $this->view->form = $form;
    }


    private function setAjaxUrl() {
        $config = Site_Config::getInstance();
        $this->view->ajaxUrl = $config->site->base .''. $config->site->dir->admin . 'cars/ajax-get-models/';
    }


    public function editAction() {
        $id = $this->_getParam('id', null);

        if ($id) {
            $this->setAjaxUrl();
            $form = new Site_Form_Car($id);
            if ($this->getRequest()->isPost()) {
                if ($this->_postBack($form) == true) {
                    $this->_helper->Redirector->gotoUrl('admin/cars/');
                }
            }
            $this->view->form = $form;
        }
        else {
            $this->_helper->Redirector->gotoUrl('admin/cars/');
        }
    }


    public function deleteAction() {
        $id = $this->_getParam('id', null);
        if ($id) {
            $model = new App_Model_Table_Auto();
            $where = $model->getAdapter()->quoteInto("id = ?", $id);
            $model->delete($where);
        }
        $this->_helper->Redirector->gotoUrl('admin/cars/');
    }


    /**
     * @param Site_Form_Car $form
     * @return boolean
     */
    private function _postBack(Site_Form_Car $form) {

        if ($form->isValid($this->getRequest()->getParams())) {
            $form->save();
            return true;
        }
        else {
            $this->view->errorElements = $form->getMessages();
        }

        return false;
    }


    /**
     * AJAX action to get concrete maker models.
     */
    public function ajaxGetModelsAction() {
        $this->_helper->viewRenderer->setNoRender();
        $data = array(
            'Success' => false
        );

        $maker_id = $this->_getParam('maker', null);
        if ($maker_id !== null) {
            $model = new App_Model_Table_Auto_Models();
            $tree = $model->getPlainTree($maker_id);
            $data['Success'] = true;
            $data['Items'] = $tree;
        }
        
        print Zend_Json_Encoder::encode($data);
        exit;
    }

}

