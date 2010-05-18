<?php

/**
 * Indexer
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Indexer extends App_Search_Indexer_Abstract {


    protected $table_index;

    protected $table_ref;

    protected $table_content;

    protected $db_prefix;

    protected $clean_relations = false;

    /**
     * Indexes model
     *
     * @var App_Search_Table_Index
     */
    private $_index;

    /**
     * Relations model
     *
     * @var App_Search_Table_Relations
     */
    private $_relation;

    /**
     * Posts model
     *
     * @var App_Search_Table_Posts
     */
    private $_posts;


    public function  __construct($config = array()) {
        parent::__construct($config);

        if (array_key_exists('db_prefix', $config)) {
            $this->setOptions('db_prefix', $config['db_prefix']);
            Zend_Registry::set('db_prefix', $config['db_prefix']);
        }

        $this->_index = new App_Search_Table_Index();
        $this->_relation = new App_Search_Table_Relations();
        $this->_posts = new App_Search_Table_Posts();
    }


    
    public function run() {

        $list = $this->_posts->fetchAll(null, null, 1);

        foreach ($list as $row) {
            $content = $this->extractContent($row);
            $this->addToIndex($row['id'], $content, $this->clean_relations);
        }
    }


    protected function extractContent($row) {
        $content = "{$row['title']} {$row['body']}";
        return $content;
    }


    /**
     * Checks if given token exists in index table. If so, returns index item
     * id. If token is not indexed returns null.
     * @param string $token
     * @return int
     */
    protected function indexExists($token) {
        $where = array(
            'word' => $token
        );

        $select = new Zend_Db_Table_Select($this->_index);
        $select->where('word = ?', $token)->limit(1);
        $result = $this->_index->fetchRow($select);
        if ($result !== null) {
            return $result->id;
        }
        return false;
    }


    /**
     * Adds token to index if it is not indexed yet.
     * @param string $token
     * @return int Index id in database
     */
    protected function indexToken($token) {
       $index_id = $this->indexExists($token);
        if ($index_id === false) {
            $data = array(
                'word' => $token
            );

            $row = $this->_index->createRow($data);
            $row->save();
            $index_id = $row->id;
        }
        return $index_id;
    }

    
    /**
     * Adds document to index. Applies post/pre filters, indexes words and
     * creates relations.
     * @param int $document_id
     * @param string $content
     */
    protected function addToIndex($document_id, $content, $autoClean = true) {

        //Cleans earlier relations.
        if ($autoClean === true) {
            $where = array(
                'post_id' => $document_id
            );
            $this->_relation->delete($where);
        }

        $filtered_content = $this->runFilters($content, self::FILTER_PRE);
        $tokens = $this->splitter->split($filtered_content);
        $tokens = $this->runFilters($tokens, self::FILTER_POST);

        foreach ($tokens as $key => $token) {
            $index_id = $this->indexToken($token);
            
            //$this->addRelation($document_id, $index_id, $key + 1);
        }
    }


    /**
     * Adds relation between document and index token.
     * @param int $document_id
     * @param int $index_id
     * @param int $location
     */
    protected function addRelation($document_id, $index_id, $location) {
        $data = array(
            'post_id' => $document_id,
            'word_id' => $index_id,
            'location' => $location
        );

        $result = $this->_relation->createRow($data);
        $result->save();
    }



    /**
     * Returns table name, adds prefix if set.
     * @param string $name
     * @return string
     */
    protected function getTable($name) {
        $table_name = 'table_' . $name;
        if (property_exists($this, $name)) {
            $prefix = '';
            if ($this->db_prefix !== null) {
                $prefix = $this->db_prefix . '_';
            }
            return $prefix . $name;
        }
        else {
            throw new Exception("Table {$name} does not exist.");
        }
    }
}
