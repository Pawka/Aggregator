<?php
/**
 * Admin
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class Site_Controller_Admin extends Site_Controller {



    public function preDispatch() {
        parent::preDispatch();
        
        $config = Site_Config::getInstance();
        $this->view->getHelper('BaseUrl')->setBaseUrl($config->site->base .''. $config->site->dir->admin);
    }


    public function prepareStyles() {
        $this->view->headLink()->setStylesheet('../css/framework.css');
        $this->view->headLink()->appendStylesheet('../css/admin.css');

    }


    public function prepareScripts() {
        $this->view->headScript()->setFile('../js/jquery-1.3.2.min.js', 'text/javascript');
    }


}
