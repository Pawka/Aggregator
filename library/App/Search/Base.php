<?php

/**
 * App_Search_BaseAbstract
 *
 * @author Povilas Balzaravičius <povilas.balzaravicius@gmail.com>
 */
abstract class App_Search_Base {

    /**
     * Element name
     *
     * Useful to generate/check form fields
     *
     * @var string
     */
    protected $_name;

    /**
     * Captcha options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Options to skip when processing options
     * @var array
     */
    protected $_skipOptions = array(
        'options',
        'config',
    );

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Constructor
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        // Set options
        if (is_array($options)) {
            $this->setOptions($options);
        } else if ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set single option for the object
     *
     * @param string $key
     * @param string $value
     * @return Zend_Form_Element
     */
    public function setOption($key, $value)
    {
        if (in_array(strtolower($key), $this->_skipOptions)) {
            return $this;
        }

        $method = 'set' . ucfirst ($key);
        if (method_exists ($this, $method)) {
            // Setter exists; use it
            $this->$method ($value);
            $this->_options[$key] = $value;
        } elseif (property_exists($this, $key)) {
            // Assume it's metadata
            $this->$key = $value;
            $this->_options[$key] = $value;
        }
        return $this;
    }

    /**
     * Set object state from options array
     *
     * @param  array $options
     * @return Zend_Form_Element
     */
    public function setOptions($options = null)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
        return $this;
    }

    /**
     * Retrieve options representing object state
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set object state from config object
     *
     * @param  Zend_Config $config
     * @return Zend_Captcha_Base
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Get optional decorator
     *
     * By default, return null, indicating no extra decorator needed.
     *
     * @return null
     */
    public function getDecorator()
    {
        return null;
    }
}
?>
