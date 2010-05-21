<?php

/**
 * Indexer
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
class App_Search_Indexer extends App_Search_Indexer_Abstract {


    protected $table_index;

    protected $table_ref;

    protected $table_content;

    protected $db_prefix;

    /**
     * If true, cleans document relations before indexing.
     * @todo Do not work.
     * @var boolean
     */
    protected $clean_relations = false;

    /**
     * If true, calls $this->_truncateTables() method
     * @see App_Search_Indexer::_truncateTables()
     * @var boolean
     */
    protected $truncate_tables = true;

    /**
     * If true, uses cache for indexing.
     * @var boolean
     */
    protected $enable_cache = false;

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    private $_cache;

    /**
     * Tag name for index cache items.
     *
     * @var string
     */
    protected $cache_tag_index = 'index';

    /**
     * Indexes model
     *
     * @var App_Search_Table_Index
     */
    private $_index;

    /**
     * Relations model
     *
     * @var App_Search_Table_Relations
     */
    private $_relation;

    /**
     * Posts model
     *
     * @var App_Search_Table_Posts
     */
    private $_posts;

    /**
     * Count of indexed documents.
     *
     * @var int
     */
    private $_indexed_documens = 0;

    /**
     * Count of indexed tokens.
     *
     * @var int
     */
    private $_indexed_tokens = 0;

    /**
     * Count of indexed relations.
     *
     * @var int
     */
    private $_indexed_relations = 0;


    public function  __construct($config = array()) {
        parent::__construct($config);


        if (array_key_exists('db_prefix', $config)) {
            $this->setOption('db_prefix', $config['db_prefix']);
            Zend_Registry::set('db_prefix', $config['db_prefix']);
        }
        $this->_index = new App_Search_Table_Index();
        $this->_relation = new App_Search_Table_Relations();
        $this->_posts = new App_Search_Table_Posts();

        if ($this->enable_cache === true) {
            $this->_initCache();
        }
    }


    /**
     * Truncates index and relation tables. Used for testing purposes.
     */
    private function _truncateTables() {
        $this->_index->truncate();
        $this->_relation->truncate();
    }


    /**
     * Indexing process
     */
    public function run() {

        if ($this->truncate_tables === true) {
            $this->_truncateTables();
        }

        $scriptTimeStart = microtime(true);
        $this->logger->info("Indexing started.");
        $list = $this->_posts->fetchAll();

        foreach ($list as $row) {
            $content = $this->extractContent($row);
            $this->addToIndex($row['id'], $content, $this->clean_relations);
        }
        $scriptTimeEnd 	= microtime(true);
        $scriptTimeRaw = $scriptTimeEnd - $scriptTimeStart;
        $scriptTime = number_format($scriptTimeRaw,4,',','.');
        $this->logger->info("Indexing finished in {$scriptTime} sec.");
        $this->logger->info("- Documents indexed: {$this->_indexed_documens}");
        $this->logger->info("- Relations created: {$this->_indexed_relations}");
        $this->logger->info("- Tokens indexed:    {$this->_indexed_tokens}");
    }


    /**
     * Creates base content from title and body strings.
     * @param array $row
     * @return string
     */
    protected function extractContent($row) {
        $content = "{$row['title']} {$row['body']}";
        return $content;
    }


    /**
     * Checks if given token exists in index table. If so, returns index item
     * id. If token is not indexed returns null.
     * @param string $token
     * @return int
     */
    protected function getIndexId($token) {

        $index_id = false;

        //If cache enabled
        if ($this->enable_cache === true) {
            $id = 'token_' . md5($token);
            $index_id = $this->_cache->load($id);
        }

        if (!$index_id) {
            $where = array(
                    'word' => $token
            );

            $select = new Zend_Db_Table_Select($this->_index);
            $select->where('word = ?', $token)->limit(1);
            $result = $this->_index->fetchRow($select);
            if ($result !== null) {
                if ($this->enable_cache === true) {
                    $this->_cache->save($result->id, $id, array($this->cache_tag_index));
                }
                $index_id = $result->id;
            }
        }
        return $index_id;
    }


    /**
     * Adds token to index if it is not indexed yet.
     * @param string $token
     * @return int Index id in database
     */
    protected function indexToken($token) {
        $index_id = $this->getIndexId($token);
        if ($index_id == false) {
            $this->_indexed_tokens++;
            $data = array(
                    'word' => $token
            );

            $row = $this->_index->createRow($data);
            $row->save();
            $index_id = $row->id;
        }
        return $index_id;
    }


    /**
     * Adds document to index. Applies post/pre filters, indexes words and
     * creates relations.
     * @param int $document_id
     * @param string $content
     */
    protected function addToIndex($document_id, $content, $autoClean = true) {

        $this->_indexed_documens++;

        //Cleans earlier relations.
        if ($autoClean === true) {
            $where = array(
                    'post_id' => $document_id
            );
            $this->_relation->delete($where);
        }

        $filtered_content = $this->runFilters($content, self::FILTER_PRE);
        $tokens = $this->splitter->split($filtered_content);
        $tokens = $this->runFilters($tokens, self::FILTER_POST);

        foreach ($tokens as $key => $token) {
            $index_id = $this->indexToken($token);
            $this->addRelation($document_id, $index_id, $key + 1);
        }
    }


    /**
     * Adds relation between document and index token.
     * @param int $document_id
     * @param int $index_id
     * @param int $location
     */
    protected function addRelation($document_id, $index_id, $location) {
        $this->_indexed_relations++;

        $data = array(
                'post_id' => $document_id,
                'word_id' => $index_id,
                'location' => $location
        );

        $result = $this->_relation->createRow($data);
        $result->save();
    }



    /**
     * Returns table name, adds prefix if set.
     * @param string $name
     * @return string
     */
    protected function getTable($name) {
        $table_name = 'table_' . $name;
        if (property_exists($this, $name)) {
            $prefix = '';
            if ($this->db_prefix !== null) {
                $prefix = $this->db_prefix . '_';
            }
            return $prefix . $name;
        }
        else {
            throw new Exception("Table {$name} does not exist.");
        }
    }


    private function _initCache() {
        $frontendOpts = array(
                'caching' => true,
                'lifetime' => 60 * 30,
                'automatic_serialization' => true
        );

        $backendOpts = array(
                'servers' =>array(
                        array(
                                'host' => '127.0.0.1',
                                'port' => 11211
                        )
                ),
                'compression' => false
        );

        $this->_cache = Zend_Cache::factory('Core', 'Memcached', $frontendOpts, $backendOpts);
        $this->_cache->clean();
    }


    /**
     * Returns cache backend.
     *
     * @return Zend_Cache_Core
     */
    public function getCache() {
        return $this->_cache;
    }
}
