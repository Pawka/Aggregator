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


    public function  __construct($config = array()) {
        parent::__construct($config);

        if (array_key_exists('db_prefix', $config)) {
            $this->setOptions('db_prefix', $config['db_prefix']);
            Zend_Registry::set('db_prefix', $config['db_prefix']);
        }
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

        $result = $this->indexExists('krabas');
        var_dump($result); exit;

        $text = $this->runFilters($text, self::FILTER_PRE);
        $words = $this->splitter->split($text);
        $words = $this->runFilters($words, self::FILTER_POST);
        var_dump($words);
    }



    /**
     * Checks if given token exists in index table. If so, returns index item
     * id. If token is not indexed returns null.
     * @param string $token
     * @return int
     */
    protected function indexExists($token) {
        $model = new App_Search_Table_Index();

        $where = array(
            'word' => $token
        );

        $select = new Zend_Db_Table_Select($model);
        $select->where('word = ?', $token)->limit(1);
        $result = $model->fetchRow($select);
        if ($result !== null) {
            return $result->id;
        }
        return null;
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
