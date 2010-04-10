<?php
/**
 * auto_makers
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class App_Model_Table_Auto_Makers extends Zend_Db_Table_Abstract {

    protected $_name = 'auto_makers';


    /**
     * Returns options array.
     * @return array
     */
    public function getOptions($addEmptyField = true) {

        $arrayTools = new Site_ArrayTools();

        $rowset = $this->fetchAll();
        $options = $arrayTools->createOptions($rowset, 'id', 'maker_name');
        if ($addEmptyField == true) {
            $options[''] = '';
            ksort($options);
        }
        return $options;
    }
    
}
?>
