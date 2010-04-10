<?php
/**
 * Auto options
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class App_Model_Table_Auto_Images extends Zend_Db_Table_Abstract {

    protected $_name = 'auto_photos';


    /**
     * Returns full path with image name, where image should be uploaded.
     * @param Zend_Db_Table_Row $row
     * @return strint
     */
    public function getWhereUploaded(Zend_Db_Table_Row $row) {
        $path = $this->getUploadPath($row->auto_id);
        $fullPath = $path . $row->image;
        return $fullPath;
    }


    /**
     * Returns upload path where autos pics should be uploaded.
     * @param int $parent_id
     * @return string
     */
    public function getUploadPath($parent_id = null) {
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


    /**
     * Returns upload url.
     * @return string
     */
    public function getUploadUrl() {
        $config = Site_Config::getInstance();
        return $config->site->base .''. $config->site->dir->upload . 'autos/';
    }

}
?>
