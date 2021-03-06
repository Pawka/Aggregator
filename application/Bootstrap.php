<?php

/**
 * Pagalbinės funkcijos
 */
require_once 'include/functions.inc.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Bootstrap autoloader
     *
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAppAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'App',
            'basePath' => dirname(__FILE__),
        ));
        return $autoloader;
    }


    /**
     * Bootstrap layout
     *
     * @return void
     */
    protected function _initLayoutHelper() {
        $this->bootstrap('frontController');

        $layout = Zend_Controller_Action_HelperBroker::addHelper(
            new Site_Controller_Action_Helper_LayoutLoader()
        );
    }


    /**
     * Bootstrap DB adapter
     *
     * @return void
     */
    protected function _initDb() {
        $config = Site_Config::getInstance();
        $db = Zend_Db::factory(
            $config->production->resources->db->adapter,
            $config->production->resources->db->toArray());

        Zend_Db_Table_Abstract::setDefaultAdapter($db);

        Zend_Registry::set('db', $db);
        $db->query("SET CHARACTER SET 'utf8'");
        $db->query("SET NAMES 'utf8'");
    }


    protected function _initLogger() {
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream("php://output");

        $format = date("Y-m-d H:i:s") . '  %priorityName% (%priority%): %message%' . PHP_EOL;
        $formatter = new Zend_Log_Formatter_Simple($format);
        $writer->setFormatter($formatter);
        $logger->addWriter($writer);
        Zend_Registry::set('logger', $logger);
    }


    /**
     *
     */
    protected function _initRouting() {
        $router = $this->frontController->getRouter();
        $router->addRoute('admin/crud',
            new Zend_Controller_Router_Route(
                'admin/:controller/:action/:id/*',
                array(
                    'module' => 'admin'
                ),
                array(
                    'id' => '\d+',
                    'action' => 'create|read|edit|delete'
                )
            )
        );
    }

    protected function _initLocale() {
        $locale = new Zend_Locale('lt_LT');
        Zend_Registry::set('Zend_Locale', $locale);
    }

}

