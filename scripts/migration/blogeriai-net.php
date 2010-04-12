<?php
/**
 * Migrates data from blogeriai.net to aggregator database
 * @author Povilas Balzaravičius <pavvka@gmail.com>
 */

define('APPLICATION_ENV', 'development');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
)));

require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

/**
 * @var Zend_Db_Adapter_Abstract
 */
$db = Zend_Registry::get('db');
$sql = new Zend_Db_Select($db);
$sql->from('blogeriai_feeds')->where('status = ?', 'active');
$results = $db->fetchAll($sql);

$logger = Zend_Registry::get('logger');
$logger->info("Migrating feeds data...");
$db->exec("TRUNCATE TABLE `feeds`");
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
    $db->insert('feeds', $data);
    $i++;
}
$logger->info("Feeds migrated: {$i}");
?>
