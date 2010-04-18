<?php

class IndexController extends Site_Controller {

    public function init() {

    }

    public function indexAction() {
        $model = new App_Model_Posts();

        $list = $model->getRecent();
        foreach ($list as $key => $row) {
            $date = new Zend_Date($row['post_date']);
            $list[ $key ]['date'] = $date->toString(Zend_Date::DATE_LONG, 'lt_LT');
        }


        $this->view->list = $list;
    }

}

