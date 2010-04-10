<?php
/**
 * ArrayTools
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class Site_ArrayTools {




    /**
     * Creates Options array, where key is $key and value is $value rows.
     * @param array $array
     * @param string $key Key row name
     * @param string $value Value row name
     */
    public function createOptions($array, $key, $value) {

        if (!isset($array[$key])) {
            throw new Exception("Key row doesn't exists in \$array.", $code);
        }

        if (!isset($array[$value])) {
            throw new Exception("Value row doesn't exists in \$array.", $code);
        }

        $result = array();
        foreach ($array as $row) {
            $result[ $row[ $key ] ] = $row[ $value ];
        }
        return $result;
    }



    /**
     * Rewrites array keys by given array element field.
     * @param array $array Data array
     * @param string $key Field name
     * @return array Rewrited array
     */
    public function rewriteKeys($array, $key) {
        $result = array();

        foreach ($array as $row) {
            $result[ $row[ $key ] ] = $row;
        }

        return $result;
    }

}
?>
