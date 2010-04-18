<?php

class IndexController extends Site_Controller {

    public function init() {

    }

    public function indexAction() {
        $model = new App_Model_Posts();

        $list = $model->getRecent();
        foreach ($list as $key => $row) {
            $list[ $key ]['date'] = $list[ $key ]['post_date']->toString(Zend_Date::DATE_LONG, 'lt_LT');
        }
        $this->view->list = $list;
    }
}

