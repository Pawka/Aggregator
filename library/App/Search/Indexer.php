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

    
    public function run() {

        $this->setOption('table_index', 'wordlist')
                ->setOption('table_ref', 'wordlocation')
                ->setOption('table_content', 'posts')
                ->setOption('db_prefix', '100');


        var_dump($this->getOptions()); exit;


        $xml =  APPLICATION_PATH . '/configs/search.xml';
        $stopwords = new Zend_Config_Xml($xml, 'production');

        $this->setFilter('Lowercase');
        $this->setFilter('CleanHTML');
        $this->setFilter('WordLength', array('min' => 3), self::FILTER_POST);
        $this->setFilter('Stopwords', array('stopwords' => $stopwords), self::FILTER_POST);


        $text = "Hei, labas. <em>Kaip tu <strong>gyveni</strong>? Kiek kaiNUoja?</em> 2.15? C++ !!!";

        $text = $this->runFilters($text, self::FILTER_PRE);
        $words = $this->splitter->split($text);
        $words = $this->runFilters($words, self::FILTER_POST);
        var_dump($words);
    }


    protected function indexExists($token) {

        //$this->_db->

        //$sql = new Zend_Db_Select($adapter)

        $this->_db->fetchOne($sql, $bind);
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
