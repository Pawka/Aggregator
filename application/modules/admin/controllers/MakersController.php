<?php

class Admin_MakersController extends Site_Controller_Admin_Catalog {

    protected function setParams() {
        $this->model = new App_Model_Table_Auto_Makers();
        $this->form = new Site_Form_Maker();
    }


    public function indexAction() {
        $model = new App_Model_Table_Auto_Makers();

        $select = $this->model->select(true);
        $params = $this->_getAllParams();
        $params['select'] = $select;
        $params['cols'] = array(
            'id' => 'ID',
            'maker_name' => 'Gamintojas'
        );

        $this->view->list = $this->view->action('list', 'interface', 'admin', $params);

    }
}

