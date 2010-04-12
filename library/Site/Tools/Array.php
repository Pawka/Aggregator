<?php
/**
 * ASite_Tools_Array
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius, 2009
 */
class Site_Tools_Array {




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
    public static function rewriteKeys($array, $key) {
        $result = array();

        foreach ($array as $row) {
            $result[ $row[ $key ] ] = $row;
        }

        return $result;
    }




    /**
     * Returns values of given key from associative array.
     * 
     * @param array $array Array of source data.
     * @param string $key Key which falues should be collected.
     * @param boolean $unique If true, returns only ounique values.
     * @return void
     */
    public static function getValues($array, $key, $unique = true) {
        $result = null;
        if (is_array($array)) {
            foreach ($array as $row) {
                if (isset($row[$key])) {
                    $result[] = $row[$key];
                }
            }
        }

        if ($unique === true && !empty($result)) {
            $result = array_unique($result);
        }
        return $result;
    }

}
?>
