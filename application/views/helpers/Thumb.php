<?php

/**
 * Helper_Thumb View helperis, grąžinantis sugeneruotą sumažintą iš cache
 * direktorijos. Jei paveiksliukas neegzistuoja, sugeneruoja jį.
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius 2009
 */
class App_View_Helper_Thumb extends Zend_View_Helper_Abstract {

    /**
     * @var Site_Config
     */
    protected $_config = null;


    /**
     * Cache failų direktorija, kur talpinami paveiksliukų cache failai.
     * @var string
     */
    private $_cacheDir = null;


    private $_cacheUrl = null;

    /**
     * Paveiksliukų talpinimo direktorija.
     * @var string
     */
    private $_imagesDir = null;


    public function  __construct() {
        $this->_config = Site_Config::getInstance();
        $this->_cacheDir = PUBLIC_PATH .'/'. $this->_config->site->dir->cache . 'img/';
        $this->_cacheUrl = $this->_config->site->base .''. $this->_config->site->dir->cache . 'img/';
        $this->_imagesDir = PUBLIC_PATH .'/'. $this->_config->site->dir->upload;
        
    }


    public function thumb($file, $width, $height, $crop = false, $params = array()) {

        $filename = $this->getCacheFilename($file, $width, $height, $crop);

        if (!$this->isCacheExists($file, $width, $height, $crop)) {
            require_once 'thumbnail.inc.php';

            
            if (file_exists($this->_imagesDir . $file)) {
                $thumb = new Thumbnail($this->_imagesDir . $file);

                if ($crop == true) {
                    $thumb->cropFromCenter(99999);
                }

                $thumb->resize($width, $height);
                $thumb->save($this->_cacheDir . $filename);
            }
        }

        $params_array = $this->prepareParams($params);

        $result = sprintf("<img src=\"%s%s\" %s />",
            $this->_cacheUrl, $filename, implode(" ", $params_array)
        );

        return $result;
    }


    /**
     * Paruošia paveiksliuko parametrų masyvą.
     * @param array $data
     * @return array
     */
    private function prepareParams($data) {
        $result = array();

        if (!empty($data)) {
            foreach ($data as $key => $row) {
                $result[] = sprintf("%s=\"%s\"", $key, htmlspecialchars($row));
            }
        }
        return $result;
    }


    /**
     * Grąžina cache failo pavadinimą, paskaičiuotą pagal paveiksliuko failo
     * vardą ir parametrus.
     * @param string $filename
     * @param int $width
     * @param int $height
     * @param int $crop
     * @return string
     */
    private function getCacheFilename($filename, $width, $height, $crop = 0) {
        $result = "{$width}_{$height}_{$crop}_" . md5("{$filename}_{$width}_{$height}") . "." . $this->getFileFormat($filename);
        return $result;
    }


    /**
     * Grąžina paveiksliuko formatą.
     * @param string $fileName Paveiksliuko failas.
     * @return string Paveiksliuko formatas.
     */
    private function getFileFormat($fileName) {
        if(stristr(strtolower($fileName),'.gif')) {
            return 'gif';
        }
        elseif(stristr(strtolower($fileName),'.jpg') || stristr(strtolower($fileName),'.jpeg')) {
            return 'jpg';
        }
        elseif(stristr(strtolower($fileName),'.png')) {
            return 'png';
        }
        else {
            return false;
        }
    }


    /**
     * Tikrina ar egzistuoja paveikslėlio cache'as.
     *
     * @param string $filename Failo vardas.
     * @param int $width Paveikslėlio plotis.
     * @param int $height Paveikslėlio aukštis.
     * @return boolean
     */
    private function isCacheExists($filename, $width, $height, $crop) {
        $hash = $this->getCacheFilename($filename, $width, $height, $crop);
        if (file_exists($this->_cacheDir . $hash)) {
            return true;
        }
        return false;
    }

}
?>
