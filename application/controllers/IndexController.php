<?php

class IndexController extends Site_Controller {

    public function init() {

    }

    public function indexAction() {
        $model = new App_Model_Page();
        $this->view->color = $model->getRandomHtmlColor();

        $pages = new App_Model_Table_Pages();
        //$pages->
    }

}

