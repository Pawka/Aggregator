<?php

class Admin_ColorsController extends Site_Controller_Admin_Catalog {


    protected function setParams() {
        $this->model = new App_Model_Table_Auto_Colors();
    }



    public function indexAction() {

        $select = $this->model->select(true);
        $params = $this->_getAllParams();
        $params['select'] = $select;
        $params['cols'] = array(
            'id' => 'ID',
            'color_name' => 'Spalva'
        );

        $this->view->list = $this->view->action('list', 'interface', 'admin', $params);
    }

}

