<?php

/**
 * Table
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Table extends Zend_Db_Table_Abstract {


     /**
     * This will automatically set table name with prefix from bootstrap file
     * @return void
     */
     protected function _setupTableName()
     {
         parent::_setupTableName();

         $index = 'db_prefix';
         if (Zend_Registry::isRegistered($index)) {
             $this->_name = Zend_Registry::get($index) . $this->_name;
             echo $this->_name;
         }
     }


     public function setTableName($new_name) {
         $this->_name = $new_name;
         $this->_setupTableName();
     }

}
