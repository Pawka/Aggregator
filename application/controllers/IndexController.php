<?php

class IndexController extends Site_Controller {

    public function init() {

    }

    public function indexAction() {
        $model = new App_Model_Posts();
        $list = $model->getRecent();
        $this->view->list = $list;
    }

}

