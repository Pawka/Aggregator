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

    /**
     * Indexes model
     *
     * @var App_Search_Table_Index
     */
    private $_index;


    public function  __construct($config = array()) {
        parent::__construct($config);

        if (array_key_exists('db_prefix', $config)) {
            $this->setOptions('db_prefix', $config['db_prefix']);
            Zend_Registry::set('db_prefix', $config['db_prefix']);
        }


        $this->_index = new App_Search_Table_Index();
    }

    
    public function run() {

        $this->setOption('table_index', 'wordlist')
                ->setOption('table_ref', 'wordlocation')
                ->setOption('table_content', 'posts');


        $xml =  APPLICATION_PATH . '/configs/search.xml';
        $stopwords = new Zend_Config_Xml($xml, 'production');

        $this->setFilter('Lowercase');
        $this->setFilter('CleanHTML');
        $this->setFilter('WordLength', array('min' => 3), self::FILTER_POST);
        $this->setFilter('Stopwords', array('stopwords' => $stopwords), self::FILTER_POST);


        $text = "Hei, labas. <em>Kaip tu <strong>gyveni</strong>? Kiek kaiNUoja?</em> 2.15? C++ !!!";

        $this->addToIndex(123, $text);

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


    protected function addToIndex($document_id, $content) {
        $filtered_content = $this->runFilters($content, self::FILTER_PRE);
        $tokens = $this->splitter->split($content);
        $tokens = $this->runFilters($tokens, self::FILTER_POST);
        var_dump($tokens);
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
