<?php
/**
 * Description of Fetcher
 *
 * @author Povilas Balzaravičius <pavvka@gmail.com>
 */
class App_Feeds_Fetcher {

    /**
     * @var App_Model_FeedsTable
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
                $this->logger->info("Fetching feed: {$content['title']}");
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
        $date_format = "Y-MM-dd H:m:s";
        $post = new App_Model_Posts();
        if ($post_id !== null) {
            $post->assignIdentifier($post_id);
        }

        $post_data = $data;
        $post_data['update_date']   = $data['update_date']->toString($date_format);
        $post_data['post_date'] = $data['create_date']->toString($date_format);
        if (!empty($data['author']) && isset($data['author'][0]['name'])) {
            $post_data['author'] = $data['author'][0]['name'];
        }
        $post->fromArray($post_data);
        $post->create_date = new Doctrine_Expression('NOW()');
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
                        'title'         => $entry->getTitle(),
                        'description'   => $entry->getDescription(),
                        'update_date'   => $entry->getDateModified(),
                        'author'        => $entry->getAuthors(),
                        'link'          => $entry->getLink(),
                        'content'       => $entry->getContent(),
                        'create_date'   => $entry->getDateCreated(),
                );
                $data['entries'][] = $edata;
            }
            $result = $data;
        }
        catch (Exception $e) {
            $feed->fetch_message = $e->getMessage();
        }

        $feed->last_fetch_date = date("Y-m-d H:i:s");
        $feed->save();
        
        return $result;
    }
}
?>
