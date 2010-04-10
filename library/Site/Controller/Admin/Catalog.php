<?php
/**
 * Catalog
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
abstract class Site_Controller_Admin_Catalog extends Site_Controller_Admin {


    /**
     * Katalogo pagrindinio modelio objektas.
     * @var Zend_Db_Table_Abstract
     */
    protected $model = null;


    /**
     * @var Zend_Form
     */
    protected $form = null;


    public function preDispatch() {
        parent::preDispatch();
        $this->setParams();

        if (($this->model instanceof Zend_Db_Table_Abstract) == false) {
            throw new Zend_Exception(get_class() . '::$model must be instance of Zend_Db_Table_Abstract.');
        }
    }


    /**
     * Nustatomi pagrindiniai katalogo modeliai.
     */
    protected abstract function setParams();



    /**
     * Default element creation action.
     */
    public function createAction() {

        if (($this->form instanceof Zend_Form) == false) {
            throw new Zend_Exception(get_class() . '::$form must be instance of Zend_Form.');
        }

        if ($this->getRequest()->isPost()) {
            if ($this->_postBack($this->form) == true) {
                $this->_helper->Redirector->gotoUrl('admin/' . $this->_getParam('contoller'));
            }
        }

        $this->view->form = $this->form;
    }


    /**
     * Default element deletion from model table.
     */
    public function deleteAction() {
        $id = $this->_getParam('id', null);
        if ($id !== null) {
            $rowset = $this->model->find($id);
            if ($rowset->count()) {
                $row = $rowset->current();
                $row->delete();
            }
        }
        $url = 'admin/' . $this->_getParam('controller');
        $this->_helper->Redirector->gotoUrl($url);
    }



    /**
     * @param Zend_Form $form
     * @return boolean
     */
    protected function _postBack($form) {

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
     * @todo Aprašyti redagavimo veiksmą
     */
    public function editAction() {
        $id = $this->_getParam('id', null);

        if ($id) {
            $form = new Site_Form_Image($this->_getParam('parent'), $id);
            if ($this->getRequest()->isPost()) {
                if ($this->_postBack($form) == true) {
                    $this->_helper->Redirector->gotoUrl('admin/images/index/parent/' . $this->_getParam('parent') . '/');
                }
            }


            $this->view->form = $form;
        }
        else {
            $this->_helper->Redirector->gotoUrl('admin/images/index/parent/' . $this->_getParam('parent') . '/');
        }
    }
}
?>
