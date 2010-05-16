<?php

require_once dirname(__FILE__) . '/../cli.php';

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
        $this->indexer = new App_Search_Indexer();
    }

    public function run() {
        $this->indexer->run();
    }
}

$indexer = new Indexer();
$indexer->run();
