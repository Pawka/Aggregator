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
             $this->_name = Zend_Registry::get($index) . '_' . $this->_name;
         }
     }


     /**
      * Truncates table.
      */
     public function truncate() {
         $sql = sprintf("TRUNCATE TABLE %s", $this->_name);
         $this->getAdapter()->query($sql, array());
     }
}
