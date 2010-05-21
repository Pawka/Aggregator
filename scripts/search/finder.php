<?php

require_once dirname(__FILE__) . '/../cli.php';


ini_set('max_execution_time', 0);
ini_set('memory_limit', '1G');


/**
 * Index data
 **/
class Finder extends Application {

    /**
     * finder
     *
     * @var App_Search_Finder
     */
    private $finder = null;

    protected function init() {

        $config = array(
            'db_prefix' => '100_stop'
        );
        $xml =  APPLICATION_PATH . '/configs/search.xml';
        $stopwordsFilter = new App_Search_Filter_Stopwords(array(
                        'stopwords' => new Zend_Config_Xml($xml, 'production')));

        $this->finder = new App_Search_Finder($config);
        $this->finder->setFilter('Lowercase')
                ->setFilter('WordLength', array('min' => 3))
                ->setFilter($stopwordsFilter)
                ->setFilter('InIndex');
    }

    public function run() {
        if (isset($_SERVER['argv']) && count($_SERVER['argv']) > 1) {
            $items = array_slice($_SERVER['argv'], 1);
            $query = implode(" ", $items);
            $this->finder->run($query);
        }
    }
}

$finder = new Finder();
$finder->run();
