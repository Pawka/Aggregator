<?php
/**
 * Auto models
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class App_Model_Table_Auto_Models extends Zend_Db_Table_Abstract {

    protected $_name = 'auto_models';


    /**
     * Returns options array.
     * @return array
     */
    public function getOptions() {

        $arrayTools = new Site_ArrayTools();

        $rowset = $this->fetchAll();
        $options = $arrayTools->createOptions($rowset, 'id', 'model_name');
        return $options;
    }



    /**
     * Returns models tree.
     * @param int $id Auto maker id.
     * @return array
     */
    public function getTree($id = null) {
        $arrayTools = new Site_ArrayTools();

        $where = null;
        if ($id !== null) {
            $where = $this->getAdapter()->quoteInto('maker_id = ?', $id);
        }

        $rowset = $this->fetchAll($where, "pos ASC")->toArray();
        $rowset = $arrayTools->rewriteKeys($rowset, 'id');

        //Atrenkam parentus.
        foreach ($rowset as $key => &$row) {
            if (trim($row['child_models']) !== "") {
                $row['childs'] = array();

                $childs = explode(',', $row['child_models']);
                foreach ($childs as $child_id) {
                    if (isset($rowset[ $child_id ])) {
                        $row['childs'][ $child_id ] = $rowset[ $child_id ];
                        unset($rowset[ $child_id ]);
                    }
                }
            }
        }

        return $rowset;
    }


    /**
     * Returns nested options tree.
     * @param int $id Maker id.
     * @return array
     */
    public function getTreeOptions($id = null) {
        $tree = $this->getTree($id);

        $result = array('' => '');

        foreach ($tree as $row) {
            if (isset($row['childs']) && !empty($row['childs'])) {
                $result[ $row['model_name'] ] = array();
                foreach ($row['childs'] as $child) {
                    $result[ $row['model_name'] ][ $child['id'] ] = "  " . $child['model_name'];
                }
            }
            else {
                $result[ $row['id'] ] = $row['model_name'];
            }
        }

        return $result;
    }



    public function getPlainTree($id = null) {
        $tree = $this->getTree($id);
        $result = array();

        foreach ($tree as $key => $row) {
            $result[$key] = $row['model_name'];
            if (isset($row['childs']) && !empty($row['childs'])) {
                foreach ($row['childs'] as $c => $child) {
                    $result[$c] = ' - ' . $child['model_name'];
                }
            }
        }

        return $result;

    }
}
?>
