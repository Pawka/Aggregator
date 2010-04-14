<?php
define('APPLICATION_ENV', 'development');
define('APPLICATION_PATH', (dirname(__FILE__) . '/../application'));
$paths = explode(PATH_SEPARATOR, get_include_path());
$paths[] = APPLICATION_PATH;
$paths[] = APPLICATION_PATH . '/../library';
set_include_path(implode(PATH_SEPARATOR, $paths));
require_once 'Zend/Application.php';

/**
 * Application class
 **/
abstract class Application {

    protected $application = null;

    function __construct() {
        // Create application, bootstrap, and run
        $this->application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
                );
        $this->application->bootstrap();
        $this->init();
    }

    protected abstract function init();
}
