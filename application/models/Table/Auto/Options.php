<?php
/**
 * Auto options
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class App_Model_Table_Auto_Options extends Zend_Db_Table_Abstract {

    protected $_name = 'auto_options';


    /**
     * Returns options array.
     * @param string $slug 
     * @return array
     */
    public function getOptionsBySlug($slug, $addEmptyField = true) {

        $arrayTools = new Site_ArrayTools();

        $where = $this->select()->where("slug = ?", $slug);
        $root = $this->fetchRow($where);

        $where = $this->select()->where("parent_id = ?", $root->id);
        $rowset = $this->fetchAll($where);
        $options = $arrayTools->createOptions($rowset, 'id', 'name');
        if ($addEmptyField == true) {
            $options[''] = '';
            ksort($options);
        }

        return $options;
    }


    public function getYears($start = 1940, $addEmptyField = true) {
        $options = array();

        for ($i = $start; $i <= date("Y") + 1; $i++) {
            $options[$i] = $i;
        }

        if ($addEmptyField == true) {
            $options[''] = '';
            ksort($options);
        }
        
        return $options;
    }



     /**
      * Techninės apžiūros datų masyvas <option> elementams.
      * @param int $months Kiek mėnesių į priekį pateikti datas.
      * @param boolean $addEmptyField
      * @return array
      */
    public function getServiceOptions($months = 24, $addEmptyField = true) {
        $options = array();

        for ($i = 0; $i <= $months; $i++) {
            $time = strtotime("+{$i} month");
            $field = date("Y-m", $time);
            $options["{$field}-01"] = $field;
        }

        if ($addEmptyField == true) {
            $options[''] = '';
            ksort($options);
        }
        
        return $options;
    }

}
?>
