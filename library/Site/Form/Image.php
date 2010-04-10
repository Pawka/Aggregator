<?php
/**
 * Image Form
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class Site_Form_Image extends Zend_Form {



    public function  __construct($parent_id = null, $id = null, $mode = 'create') {

        $this->setAttrib('enctype', 'multipart/form-data');

        $auto_id = new Zend_Form_Element_Hidden('auto_id');
        $auto_id->setValue($parent_id);

        $pos = new Zend_Form_Element_Hidden('pos');
        $pos->setValue(1);


        $image = new Zend_Form_Element_File('image');
        $image->setLabel('Nuotrauka')
            ->setRequired(true)
            ->setValueDisabled(true);

        $submit = new Zend_Form_Element_Submit($mode);

        if ($id !== null) {
            $this->id = new Zend_Form_Element_Hidden('id');
            $this->id->setValue($id);
        }

        $this->addElements(array($auto_id, $pos, $image, $submit));


        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            'Form'
        ));


        if ($id != null) {
            $this->_mapItem($id);
        }
    }

    public function save() {
        $model = new App_Model_Table_Auto_Images();

        $values = $this->getValues();
        $values['image'] = $this->_generateFilename($values['image']);

        //UPDATE
        if ($this->getValue('id') > 0) {
            $rowset = $model->find($this->getValue('id'));
            if ($rowset->count() > 0) {
                $row = $rowset->current();
                $row->setFromArray($values);
                $row->modified = new Zend_Db_Expr('NOW()');
                $row->save();
                return true;
            }
            return false;
        }
        //INSERT
        else {
            $row = $model->createRow($values);
            //$row->created = new Zend_Db_Expr('NOW()');
            //$row->modified = new Zend_Db_Expr('NOW()');
            $row->save();
        }
        $isUploaded = $this->_handleUpload($row->auto_id, $values['image']);
        if ($isUploaded == false) {
            $row->delete();
        }
    }


    /**
     * Retrieve auto pictures upload path.
     * @param int $parent_id
     * @return string Upload path
     */
    private function _getUploadPath($parent_id = null) {
        $path = PUBLIC_PATH . '/uploads/auto/';
        if ($parent_id !== null) {
            $newPath = $path . $parent_id . '/';
            if (!file_exists($newPath)) {
                mkdir($newPath);
            }

            chmod($newPath, 0777);
            $path = $newPath;
        }

        return $path;
    }


    private function _generateFilename($filename) {
        $tools = new Site_Tools();
        $ext = $tools->getExtension($filename);
        $hash = md5($filename);

        $newFilename = date("Ymd") . '-' . substr($hash, 0, 10) . '.' . $ext;
        return $newFilename;
    }



    private function _handleUpload($parent_id = null, $newFilename = null) {
        $model = new App_Model_Table_Auto_Images();
        $uploadPath = $model->getUploadPath($parent_id);
        $this->image->setDestination($uploadPath);
        if ($this->image->receive()) {

            if ($newFilename !== null) {
                $name = $this->image->getFileName();
                $rename = new Zend_Filter_File_Rename(array('target' => $uploadPath . $newFilename, 'overwrite' => true));
                $rename->filter($name);
            }
            return true;
        }
        else {
            return false;
        }
    }
}