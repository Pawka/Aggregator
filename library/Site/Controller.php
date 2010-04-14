<?php

/**
 * Site controller
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class Site_Controller extends Zend_Controller_Action {

/**
 * DB objektas
 * @var Zend_Db_Adapter_Abstract
 */
    public $db = null;

    public function preDispatch() {
        $this->prepareDb();
        $this->prepareMetadata();
        $this->prepareView();
        $this->prepareStyles();
        $this->prepareScripts();
        $this->view->params = $this->_getAllParams();
    }


    /**
     * Nustato Zend_View custom parametrus.
     */
    public function prepareView() {
        $config = Site_Config::getInstance();
        $this->set('config', array(
            'site' => $config->site
        ));
    }


    /**
     * Nustato Zend_Db duomenų bazės kontrolerį į klasės objektą.
     */
    public function prepareDb() {
        $this->db = Zend_Registry::get('db');
    }


    /**
     * Paruošia puslapio metainformaciją
     */
    public function prepareMetadata() {
        $config = Site_Config::getInstance();
        $this->view->headMeta()
            ->setName('description',  $config->site->description)
            ->setName('keywords',     $config->site->keywords)
            ->setName('author', "Povilas Balzaravičius, Lithuania")
            ->setHttpEquiv('content-type', 'text/html; charset=UTF-8')
            ->setHttpEquiv('content-style-type', 'text/css');

        $this->view->headTitle($config->site->title)
            ->setSeparator(' | ');
    }


    /**
     * Nustato loadinamus skriptus.
     */
    public function prepareScripts() {
        $this->view->headScript()->setScript('js/jquery-1.3.2.min.js', 'text/javascript');
    }


    /**
     * Nustato loadinamus stilius.
     */
    public function prepareStyles() {
        $this->view->headLink()
            ->setStylesheet('css/framework.css');
        $this->view->headLink()
            ->appendStylesheet('css/style.css');
    }



    /**
     * Nustato Zend_View komponentui reikšmes.
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value) {
        $this->view->$name = $value;
    }


    /**
     * Nuskaito reikšmę iš Zend_View komponento.
     * @param string $name
     * @return mixed
     */
    public function get($name) {
        return $this->view->$name;
    }


    /**
     * Returns current controller url
     * @param boolean $addModule Add module name to url.
     * @param string $controllerName Set custom controller name.
     * @return string
     */
    public function getControllerUrl($controllerName = null, $addModule = false) {
        $request = $this->getRequest();

        $result = '';
        if ($addModule === true) {
            $result .= $request->getModuleName() . '/';
        }

        if ($controllerName !== null) {
            $result .= $controllerName . '/';
        }
        else {
            $result .=  $request->getControllerName() . '/';
        }

        return $result;
    }


    /**
     * Returns current action url.
     * @param boolean $addModule Add module name to url.
     * @param string $controllerName Set custom controller name.
     * @param string $actionName Set custom action name.
     * @return string
     */
    public function getActionUrl($controllerName = null, $actionName = null, $addModule = false) {
        $url = $this->getControllerUrl($controllerName, $addModule);
        if ($actionNamme !== null) {
            $url .= $actionName;
        }
        else {
            $url .= $this->getRequest()->getActionName();
        }
        return $url;
    }

}
?>
