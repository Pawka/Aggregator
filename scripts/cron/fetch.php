<?php


require_once dirname(__FILE__) . '/../cli.php';

/**
 * Fetch data from RSS feeds
 **/
class Fetcher extends Application {

    /**
     * fetcher 
     * 
     * @var App_Feeds_Fetcher
     */
    private $fetcher = null;

    protected function init() {
        $this->fetcher = new App_Feeds_Fetcher();
    }

    public function run() {
        $this->fetcher->fetch();
    }
}

$fetcher = new Fetcher();
$fetcher->run();
