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

    /**
     * logger 
     * 
     * @var Zend_Log
     */
    private $logger = null;

    public function  __construct() {
        $this->logger = Zend_Registry::get('logger');
        set_time_limit(0);
    }


    public function fetch() {
        $query = Doctrine_Query::create();
        $query->from('App_Model_Feeds')->where('active = 1');
        foreach ($query->execute() as $row) {
            $content = $this->readFeed($row);
            if ($content !== null) {
                $this->savePosts($row, $content['entries']);
            }
            $row->free();
        }
    }


    /**
     * Saves posts fetched from RSS feed.
     * @param App_Model_Feeds $feed_row
     * @param array $posts
     */
    protected function savePosts($feed_row, $posts) {
        if (empty($posts)) {
            return null;
        }

        $links = Site_Tools_Array::getValues($posts, 'link');
        $existingPosts = array();
        if (!empty($links)) {
            $q = Doctrine_Query::create()->select("*")->from('App_Model_Posts')
                    ->whereIn('link', $links);
            $existingPosts = $q->fetchArray();
            $existingPosts = Site_Tools_Array::rewriteKeys($existingPosts, 'link');
        }

        foreach ($posts as $row) {
            $post_id = isset($existingPosts[ $row['link'] ]) ? $existingPosts[ $row['link'] ]['id'] : null;
            $row['feed_id'] = $feed_row->id;
            $this->savePost($row, $post_id);
        }

    }


    protected function savePost($data, $post_id = null) {
        $post = new App_Model_Posts();
        
        if ($post_id !== null) {
            $post->assignIdentifier($post_id);
        }
        else {
            $post->create_date = new Doctrine_Expression('NOW()');
        }

        $post->fromArray($data);
        $post->update_date = new Doctrine_Expression('NOW()');
        try {
            $post->save();
        }
        catch (Exception $e) {
            $this->logger->err($e->getMessage());
        }
        $post->free();
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
