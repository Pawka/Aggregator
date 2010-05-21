<?php

/**
 * App_Search_Finder
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Finder extends App_Search_Finder_Abstract {

    const MATCH_ALL_KEYWORDS = 1;
    const MATCH_ANY_KEYWORDS = 2;

    /**
     * @var App_Search_Table_Relations
     */
    private $_model_relations;

    /**
     * @var App_Search_Table_Index
     */
    private $_model_index;

    /**
     * @var App_Search_Table_Posts
     */
    private $_model_posts;


    /**
     * Sets type of search mode.
     * @var int
     */
    protected $search_mode;

    public function  __construct($config = array()) {
        parent::__construct($config);

        if (array_key_exists('search_mode', $config)) {
            $this->setOption('search_mode', $config['search_mode']);
        }
        else {
            $this->setOption('search_mode', self::MATCH_ANY_KEYWORDS);
        }

        $this->_model_relations = new App_Search_Table_Relations();
        $this->_model_index = new App_Search_Table_Index();
        $this->_model_posts = new App_Search_Table_Posts();
    }


    public function run($query) {
        $words = $this->_filterQuery($query);
        dump($words);
        $rowset = $this->_getMatchedRows($words);
        pa($rowset);
    }


    /**
     * Splits content query to tokens and applies filters to them.
     * @param string $query
     * @return array
     */
    private function _filterQuery($query) {
        $words = $this->getSplitter()->split($query);
        return $this->runFilters($words);
    }



    private function _getMatchedRows($words) {

        $query = new Zend_Db_Select($this->_model_relations->getAdapter());

        //Resets array keys if they were set not propertly.
        $list = array_values($words);

        $i = 0;
        foreach ($list as $row) {
            $token_id = $this->getIndexId($row);
            
            //If token not exits, return empty result.
            if ($token_id == null) {
                switch ($this->search_mode) {
                    case self::MATCH_ALL_KEYWORDS:
                        return null;
                        break;

                    default:
                    case self::MATCH_ANY_KEYWORDS:
                        continue 2;
                        break;
                }
            }

            $name = $this->_model_relations->getTableName();
            $cols = array();
            if ($i == 0) {
                $cols[] = "w{$i}.post_id";
            }
            $cols[] = "w{$i}.location AS location_{$i}";
            $query->from(array("w{$i}" => $name), $cols);
            $query->where("w{$i}.word_id = ?", $token_id);
            if ($i > 0) {
                $query->where("w0.post_id = w{$i}.post_id");
            }
            $i++;
        }

        pa($query->__toString());
        $result = $this->_db->fetchAll($query);

        return $result;
    }


    protected function getIndexId($token) {
        $item = $this->_model_index->fetchRow("word = '{$token}'");
        if ($item) {
            $this->logger->info("Found token '{$token}' #{$item->id}");
            return $item->id;
        }
        return null;
    }
}
?>
