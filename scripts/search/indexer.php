<?php

require_once dirname(__FILE__) . '/../cli.php';


ini_set('max_execution_time', 0);
ini_set('memory_limit', '1G');


/**
 * Index data
 **/
class Indexer extends Application {

    /**
     * indexer
     *
     * @var App_Search_Indexer
     */
    private $indexer = null;

    protected function init() {

        $config = array(
            'db_prefix' => '5000',
        );

        $xml =  APPLICATION_PATH . '/configs/search.xml';
        $stopwords = new Zend_Config_Xml($xml, 'production');
        
        $this->indexer = new App_Search_Indexer($config);
        $this->indexer->setFilter('Lowercase')
            ->setFilter('CleanHTML')
            ->setFilter('WordLength', array('min' => 3),
                    App_Search_Indexer::FILTER_POST)
            ->setFilter('Stopwords', array('stopwords' => $stopwords),
                    App_Search_Indexer::FILTER_POST);

    }

    public function run() {
        $this->indexer->run();
    }
}

$indexer = new Indexer();
$indexer->run();
