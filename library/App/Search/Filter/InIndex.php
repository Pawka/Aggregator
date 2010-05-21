<?php

/**
 * App_Search_Filter_InIndex
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Filter_InIndex extends App_Search_Filter {

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * @var App_Search_Table_Index
     */
    protected $_model_index;

    protected $_cache_tag = 'filter_inindex';

    protected $_add_ids = true;


    public function  __construct($config = array()) {
        parent::__construct($config);

        $this->_model_index = new App_Search_Table_Index();
    }


    /**
     * Filters words that exists in index table.
     * 
     * @param string $content
     * @return string
     */
    public function run($content) {
        if (is_array($content)) {
            $result = array();

            foreach ($content as $row) {
                $item_id = $this->_existsInIndex($row);
                if ($item_id) {

                    if ($this->_add_ids) {
                        $result[$item_id] = $row;
                    }
                    else {
                        $result[] = $row;
                    }
                }
            }
        }
        else {
            $result = $this->_existsInIndex($content);
        }
        return $result;
    }

    
    /**
     * Checks if index exists in table.
     * @param string $token
     * @return int Index id in db table
     */
    private function _existsInIndex($token) {

        $index_id = false;

        //If cache enabled
        if ($this->_cache !== null) {
            $id = 'token_' . md5($token);
            $index_id = $this->_cache->load($id);
        }

        if (!$index_id) {
            $where = array('word' => $token);

            $select = new Zend_Db_Table_Select($this->_model_index);
            $select->where('word = ?', $token)->limit(1);
            $result = $this->_model_index->fetchRow($select);
            if ($result !== null) {
                if ($this->_cache !== null) {
                    $this->_cache->save($result->id, $id, array($this->_cache_tag));
                }
                $index_id = $result->id;
            }
        }
        return $index_id;
    }
}
?>
