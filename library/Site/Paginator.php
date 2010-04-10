<?php

class Site_Paginator {

    /**
     * View object to use.
     * @var Zend_View
     */
    private $_view = null;

    /**
     * Paginator template name to use.
     * @var string
     */
    private $_template = null;

    /**
     * Items per page. Setting from config.
     * @var int
     */
    private $_itemsPerPage     = null;

    public function __construct($view, $template = 'paginator.phtml') {
        $config = Site_Config::getInstance();
        $this->_view        = $view;
        $this->_template    = $template;
        $this->_itemsPerPage = $config->admin->items_per_page;
    }


    /**
     * Nustato kiek objektų rodyti viename puslapyje.
     * @param int $count Puslapyje rodomų objektų kiekis.
     */
    public function setItemsPerPage($count) {
        $this->_itemsPerPage = abs($count);
    }


    /**
     * Nustato puslapiavimo šabloną.
     * @param string $template Šablono pavadinimas.
     */
    public function setTemplate($template) {
        $this->_template = $template . '.phtml';
    }



    /**
     * Initialize paginator.
     * @param Zend_Db_Select $data
     * @param int $pageNumber
     * @return Zend_Paginator
     */
    public function init($data, $pageNumber) {
        $paginator = Zend_Paginator::factory($data);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage($this->_itemsPerPage);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial($this->_template);
        $paginator->setView($this->_view);
        return $paginator;
    }


    /**
     * Deprecated.
     * @see Site_Paginator::init();
     */
    public function createPaginator($data, $pageNumber) {
        return $this->init($data, $pageNumber);
    }
}