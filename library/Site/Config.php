<?php
/**
 * Site_Config
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius 2009
 */
class Site_Config {


    /**
     * @var Site_Config Singleton instance.
     */
    protected static $_instance = null;


    /**
     * @var Zend_Config
     */
    private $_config = null;


    /**
     * Perdengtas konstruktorius.
     */
    protected function  __construct() {
        $options = array(
            'allowModifications' => true
        );

        $this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', null, $options);
        $this->_prepareDynamicValues();
    }


    /**
     * Zend_Config reikšmių pasiekimas.
     *
     * @param string $name
     * @return string
     */
    public function  __get($name) {
        return $this->_config->$name;
    }



    /**
     * Returns singleton instance
     *
     * @return Site_Config
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * Reset the singleton instance
     *
     * @return void
     */
    public static function resetInstance()
    {
        self::$_instance = null;
    }


    /**
     * Paruošia dinamines reikšmes.
     */
    private function _prepareDynamicValues() {
        $base_url = $this->site->base;

        //$this->_config->site->url->admin = $base_url . $this->site->dir->admin;
    }
}
?>
