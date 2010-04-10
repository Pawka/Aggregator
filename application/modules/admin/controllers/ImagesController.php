<?php

class Admin_ImagesController extends Site_Controller_Admin {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $model = new App_Model_Table_Auto_Images();

        $page       = $this->_getParam('page', 1);
        $sort       = $this->_getParam('sort', 'id');
        $order      = $this->_getParam('order', 'desc');

        $data = $model->select()->order("{$sort} {$order}")->where('auto_id = ?', $this->_getParam('parent'));
        $paginator = new Site_Paginator($this->view);
        $this->view->paginator = $paginator->createPaginator($data, $page);
        $this->view->uploadPath = $model->getUploadPath();
   }


    public function createAction() {
        $parent_id = (int)$this->_getParam('parent');
        $form = new Site_Form_Image($parent_id);

        if ($this->getRequest()->isPost()) {
            if ($this->_postBack($form) == true) {
                $this->_helper->Redirector->gotoUrl('admin/images/index/parent/' . $this->_getParam('parent') . '/');
            }
        }

        $this->view->form = $form;
    }


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


    public function deleteAction() {
        $parent_id = null;
        $id = $this->_getParam('id', null);
        if ($id) {
            $model = new App_Model_Table_Auto_Images();
            $rowset = $model->find($id);
            if ($rowset->count()) {
                $row = $rowset->current();
                $parent_id = $row->auto_id;
                $fullPath = $model->getWhereUploaded($row);
                @unlink($fullPath);
                $row->delete();
            }
        }
        $url = 'admin/images/index/';
        if ($parent_id !== null) {
            $url .= "parent/{$parent_id}/";
        }
        $this->_helper->Redirector->gotoUrl($url);
    }


    /**
     *
     * @param Site_Form_Image $form
     * @return boolean
     */
    private function _postBack($form) {

        if ($form->isValid($this->getRequest()->getParams())) {
            $form->save();
            return true;
        }
        else {
            $this->view->errorElements = $form->getMessages();
        }

        return false;
    }
}

