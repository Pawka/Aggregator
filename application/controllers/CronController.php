<?php

class CronController extends Site_Controller {
    
    public function init() {
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction() {
        
    }

    public function fetchAction() {
        $fetcher = new App_Feeds_Fetcher();
        $fetcher->fetch();

    }
}
