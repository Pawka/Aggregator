<?php

/**
 * Indexer
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Indexer extends App_Search_Indexer_Abstract {


    public function run() {

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

}
