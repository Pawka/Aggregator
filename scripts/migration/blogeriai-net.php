<?php
/**
 * Migrates data from blogeriai.net to aggregator database
 * @author Povilas Balzaravičius <pavvka@gmail.com>
 */

require_once dirname(__FILE__) . '/../cli.php';

/**
 * Db migration
 **/
class Migration extends Application
{
    
    /**
     * db 
     * @var Zend_Db_Adapter_Abstract 
     */
    private $db = null;
    
    /**
     * logger  
     * @var Zend_Log 
     */
    private $logger = null;

    public function init()
    {
        $this->db = Zend_Registry::get('db');
        $this->logger = Zend_Registry::get('logger');
    }

    public function migrate($type) {
        $methodName = "migrate{$type}";
        $this->logger->info("Calling method {$methodName}");
        $start = $this->getTime();
        if (method_exists($this, $methodName)) {
            $this->$methodName();
        }
        $end = $this->getTime();
        $this->logger->info("Script finished in ". round($end - $start, 4));
    }
    
    /**
     * Migrates data from blogeriai_feeds db table to feeds.
     * 
     * @return void
     */
    protected function migrateFeeds($truncate = true) {
        $sql = new Zend_Db_Select($this->db);
        $sql->from('blogeriai_feeds');
        $results = $this->db->fetchAll($sql);

        $this->logger->info("Migrating feeds data...");
        if ($truncate === true) {
            $this->db->exec("TRUNCATE TABLE `feeds`");
        }
        $i = 0;
        foreach ($results as $row) {
            $data = array(
                    'title'     => $row['title'],
                    'rss_url'   => $row['rss_url'],
                    'url'       => $row['link'],
                    'create_date' => new Zend_Db_Expr('NOW()'),
                    'update_date' => new Zend_Db_Expr('NOW()'),
                    'active'    => 1,
                    );
            if (strlen($data['title']) == 0) {
                $data['title'] = $data['url'];
            }
            $this->db->insert('feeds', $data);
            $i++;
        }
        $this->logger->info("Feeds migrated: {$i}");
    }

    /**
     * migratePosts 
     * 
     * @param bool $truncate 
     * @return void
     */
    protected function migratePosts($truncate = true)
    {
        $limit = 100;
        $offset = 0;
        $loop = true;
        
        $this->logger->info("Migrating posts data...");
        if ($truncate === true) {
            $this->db->exec("TRUNCATE TABLE `posts`");
        }

        while ($loop === true) {
            $sql = new Zend_Db_Select($this->db);
            $sql->from('blogeriai_posts')->limit($limit, $offset);
            $results = $this->db->fetchAll($sql);
            if (count($results) == 0) {
                $loop = false;
                break;
            }
            $i = $offset;
            foreach ($results as $row) {
                $data = array(
                        'title'         => $row['title'],
                        'link'          => $row['link'],
                        'body'          => $row['body'],
                        'excerpt'       => null,
                        'body_cleared'  => strip_tags($row['body']),
                        'create_date'   => $row['created_at'],
                        'update_date'   => $row['updated_at'],
                        'feed_id'       => $row['feed_id'],
                        'author'        => $row['author']
                        );
                try {
                    $this->db->insert('posts', $data);
                    $i++;
                }
                catch (Exception $e) {
                    $this->logger->err($e->getMessage());
                }
            }
            $offset += $limit;
        }
        $this->logger->info("Posts migrated: {$i}");
    }


    /**
     *  Returns current time.
     */
    protected function getTime() { 
        $timer = explode( ' ', microtime() ); 
        $timer = $timer[1] + $timer[0]; 
        return $timer; 
    }
}
$migration = new migration();
//$migration->migrate('Feeds');
$migration->migrate('Posts');
?>
