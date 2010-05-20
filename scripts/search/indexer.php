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
            'db_prefix' => 'mi_2500',
            'enable_cache' => false
        );

        $this->indexer = new App_Search_Indexer($config);

        $xml =  APPLICATION_PATH . '/configs/search.xml';
        $stopwords = new Zend_Config_Xml($xml, 'production');
        $stopwordsFilter = new App_Search_Filter_Stopwords(array(
            'stopwords' => $stopwords,
            //'cache' => $this->indexer->getCache(),
            ));

        $this->indexer->setFilter('Lowercase')
            ->setFilter('CleanHTML')
            ->setFilter('WordLength', array('min' => 3),
                    App_Search_Indexer::FILTER_POST)
            ->setFilter($stopwordsFilter, array(),
                    App_Search_Indexer::FILTER_POST);

    }

    public function run() {
        $this->indexer->run();
    }
}

$indexer = new Indexer();
$indexer->run();
