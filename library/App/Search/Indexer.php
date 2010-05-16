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

        $this->setFilter('Lowercase')
                ->setFilter('CleanHTML')
                ->setFilter('WordLength', array('min' => 3, 'max' => 10, 'splitter' => 'Regexp'))
                ->setFilter('Stopwords', array('stopwords' => $stopwords, 'splitter' => 'Regexp'));
        
        $text = "Hei, labas. <em>Kaip tu <strong>gyveni</strong>? Kiek kaiNUoja?</em> 2.15? C++ !!!";
        $this->logger->info($text);
        $text = $this->runFilters($text);
        $this->logger->info($text);

        var_dump($this->splitter->split($text));
    }

}
