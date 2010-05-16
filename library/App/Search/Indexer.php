<?php

/**
 * Indexer
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Indexer extends App_Search_Indexer_Abstract {


    public function run() {

        $this->setFilter('Lowercase')
                ->setFilter('CleanHTML')
                ->setFilter('WordLength', array('min' => 4, 'max' => 4, 'splitter' => 'Regexp'));
        
        $text = "Hei, labas. <em>Kaip tu <strong>gyveni</strong>? Kiek kaiNUoja?</em> 2.15? C++ !!!";
        $this->logger->info($text);
        $text = $this->runFilters($text);
        $this->logger->info($text);


        var_dump($this->splitter->split($text));
    }

}
