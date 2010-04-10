<?php
/**
 * Tools
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class Site_Tools {

    /**
     * Returns file extension from filename
     * @param string $filename Filename of file
     * @return string
     */
    public function getExtension($filename) {
        $filename = strtolower($filename) ;
        $exts = split("[/\\.]", $filename) ;
        $n = count($exts)-1;
        $exts = $exts[$n];
        return $exts;
    }
}
?>
