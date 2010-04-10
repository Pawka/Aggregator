<?php
/**
 * Description of Fetcher
 *
 * @author Povilas Balzaravičius <pavvka@gmail.com>
 */
class Site_Feeds_Fetcher {

    /**
     * @var Site_Model_FeedsTable
     */
    private $table = null;

    public function  __construct() {

    }


    public function fetch() {
        $query = Doctrine_Query::create();
        $query->from('App_Model_Feeds')->where('active = 1');
        foreach ($query->execute() as $row) {
            $content = $this->readFeed($row);
            $this->writePosts($row, $content);
        }
    }


    protected function writePosts($feed_row, $posts) {
        $links = $this->getFeedLinks($posts['entries']);
    }


    protected function getFeedLinks($posts) {
        
    }


    /**
     * Reads RSS content from given feed.
     * @param App_Model_Feeds $feed RSS feed row from db for reading.
     * @return array
     */
    protected function readFeed($feed) {
        $feed_url = $feed->rss_url;
        $feed->fetch_message = null;

        $error_message = null;
        $result = null;
        try {
            $source = Zend_Feed_Reader::import($feed_url);
            $data = array(
                    'title'        => $source->getTitle(),
                    'link'         => $source->getLink(),
                    'dateModified' => $source->getDateModified(),
                    'description'  => $source->getDescription(),
                    'language'     => $source->getLanguage(),
                    'entries'      => array(),
            );

            foreach ($source as $entry) {
                $edata = array(
                        'title'        => $entry->getTitle(),
                        'description'  => $entry->getDescription(),
                        'dateModified' => $entry->getDateModified(),
                        'authors'       => $entry->getAuthors(),
                        'link'         => $entry->getLink(),
                        'content'      => $entry->getContent()
                );
                //pa($edata);
                $data['entries'][] = $edata;
            }
            $result = $data;
        }
        catch (Zend_Feed_Exception $e) {
            $feed->fetch_message = $e->getMessage();
        }

        $feed->last_fetch_date = date("Y-m-d H:i:s");
        $feed->save();
        
        return $result;
    }
}
?>
