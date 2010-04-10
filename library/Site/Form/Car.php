<?php
/**
 * Car Form
 *
 * @author Povilas Balzaravičius
 * @copyright Povilas Balzaravičius
 */
class Site_Form_Car extends Zend_Form {


/**
 * Site_ArrayTools instance.
 * @var Site_ArrayTools
 */
    protected $arrayTools = null;

    public function  __construct($id = null, $mode = 'create') {
        $this->arrayTools = new Site_ArrayTools();

        $this->_addBodyTypes();
        $this->_addMakers();
        $this->_addModels();
        $this->_addYear();
        $this->_addPrice();
        $this->_addCurrency();
        $this->_addMaintain();
        $this->_addServiceUntil();
        $this->_addMileage();
        $this->_addWheelRight();
        $this->_addLeasing();

        $this->_addFuelType();
        $this->_addEngine();
        $this->_addPower();
        $this->_addColors();
        $this->_addMetallic();
        $this->_addDoors();
        $this->_addGearbox();
        $this->_addModification();
        $this->_addAWD();
        
        $this->_addFuelConsumption();
        $this->_addOptions();

        $this->_addNotes();
        $this->_addContacts();

        $this->submit = new Zend_Form_Element_Submit($mode);


        $maker_id = $this->getElement('maker')->getValue();
        if ($maker_id > 0) {
            $options = $model->getTreeOptions($maker_id);
            $this->getElement('model')->addMultiOptions($options);
        }


        if ($id !== null) {
            $this->id = new Zend_Form_Element_Hidden('id');
            $this->id->setValue($id);

            $model = new App_Model_Table_Auto_Models();

            $this->_mapItem($id);
            $maker_id = $this->getElement('maker')->getValue();
            if ($maker_id > 0) {
                $options = $model->getTreeOptions($maker_id);
                $this->getElement('model')->addMultiOptions($options);
            }
        }



        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            'Form'
        ));
    }


    private function _mapItem($id) {
        $model = new App_Model_Table_Auto();
        $rowset = $model->find($id);
        if ($rowset->count() > 0) {
            $row = $rowset->current();
            $data = $row->toArray();

            foreach ($data as $k => $r) {
                if ($this->$k) {
                    switch ($k) {
                        case 'options':
                            $options = explode(',', $r);
                            $subForm = $this->getSubForm('options');
                            if ($subForm && !empty($options)) {
                                foreach ($options as $o) {
                                    $name = 'opt_' . $o;
                                    if (isset($subForm->$name)) {
                                        $subForm->$name->setChecked(true);
                                    }
                                }
                            }

                            break;

                        default:
                            $this->$k->setValue($r);
                            break;
                    }
                }
            }
        }
    }


    private function _addBodyTypes() {
        $makers = new App_Model_Table_Auto_BodyTypes();
        $options = $makers->getOptions();
        $item = new Zend_Form_Element_Select('bodytype');
        $item->addMultiOptions($options)
            ->setLabel('Kėbulo tipas');

        $this->addElement($item);
    }


    private function _addMakers() {
        $makers = new App_Model_Table_Auto_Makers();
        $options = $makers->getOptions();
        $item = new Zend_Form_Element_Select('maker', $rowset);
        $item->addMultiOptions($options)
            ->setLabel('Gamintojas')
            ->setRequired(true);

        $this->addElement($item);
    }


    private function _addModels() {
        $model = new App_Model_Table_Auto_Models();
        //$options = $model->getTreeOptions();
        $item = new Zend_Form_Element_Select('model');
        $item->setLabel('Modelis')
            ->setRegisterInArrayValidator(false)
            ->setRequired(true);

        $this->addElement($item);
    }


    private function _addColors() {
        $makers = new App_Model_Table_Auto_Colors();
        $options = $makers->getOptions();
        $item = new Zend_Form_Element_Select('color', $rowset);
        $item->addMultiOptions($options)
            ->setLabel('Spalva');

        $this->addElement($item);
    }

    private function _addFuelType() {
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getOptionsBySlug('fuel_type');
        $item = new Zend_Form_Element_Select('fuel_type');
        $item->addMultiOptions($options)
            ->setLabel('Kuro tipas');

        $this->addElement($item);
    }


    private function _addYear() {
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getYears(1940, true);
        $element = new Zend_Form_Element_Select('year');
        $element->setLabel('Gamybos metai')
            ->setRequired(true)
            ->addValidator('Digits')
            ->addMultiOptions($options);

        $this->addElement($element);
    }

    private function _addPrice() {
        $element = new Zend_Form_Element_Text('price');
        $element->setLabel('Kaina')->setRequired(true)
            ->addValidator('Digits');

        $this->addElement($element);
    }


    private function _addCurrency() {
        $options = array (
            'LTL' => 'LTL',
            'EUR' => 'EUR',
            'USD' => 'USD'
        );

        $element = new Zend_Form_Element_Select('currency');
        $element->setLabel('Valiuta')
            ->setRequired(true)
            ->addMultiOptions($options);

        $this->addElement($element);
    }


    /**
     * Eksplotuota
     */
    private function _addMaintain() {
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getOptionsBySlug('maintain');

        $element = new Zend_Form_Element_Select('maintain');
        $element->addMultiOptions($options);
        $element->setLabel('Eksplotuota');
        $this->addElement($element);

    }


    /**
     * Techninė apžiūra
     */
    private function _addServiceUntil() {
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getServiceOptions(24, true);

        $element = new Zend_Form_Element_Select('service_until');
        $element->setLabel('TA galioja iki')
            ->addMultiOptions($options);
        $this->addElement($element);
    }

    /**
     * Techninė apžiūra
     */
    private function _addLeasing() {
        $element = new Zend_Form_Element_Checkbox('leasing');
        $element->setLabel('Lizingas');
        $this->addElement($element);
    }


    /**
     * Variklio tūris
     */
    private function _addEngine() {
        $element = new Zend_Form_Element_Text('engine');
        $element->setLabel('Variklio tūris l.');
        $this->addElement($element);
    }


    /**
     * Galia
     */
    private function _addPower() {
        $element = new Zend_Form_Element_Text('power');
        $element->setLabel('Galia');
        $this->addElement($element);
    }


    /**
     * Duryts
     */
    private function _addDoors() {
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getOptionsBySlug('doors');

        $element = new Zend_Form_Element_Select('doors');
        $element->addMultiOptions($options);
        $element->setLabel('Durys');
        $this->addElement($element);
    }


    /**
     * Greičių dėžė
     */
    private function _addGearbox() {
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getOptionsBySlug('gearbox');

        $element = new Zend_Form_Element_Select('gearbox');
        $element->addMultiOptions($options);
        $element->setLabel('Greičių dėžė');
        $this->addElement($element);
    }


    /**
     * Galia
     */
    private function _addModification() {
        $element = new Zend_Form_Element_Text('engine_mod');
        $element->setLabel('Modifikacija');
        $this->addElement($element);
    }


    /**
     * Visi varantys ratai
     */
    private function _addAWD() {
        $element = new Zend_Form_Element_Checkbox('awd');
        $element->setLabel('Visi varantys ratai');
        $this->addElement($element);
    }

    /**
     * Metalic
     */
    private function _addMetallic() {
        $element = new Zend_Form_Element_Checkbox('metallic');
        $element->setLabel('Metallic');
        $this->addElement($element);
    }



    /**
     * Vairas dešinėje
     */
    private function _addWheelRight() {
        $element = new Zend_Form_Element_Checkbox('wheel');
        $element->setLabel('Vairas dešinėje');
        $this->addElement($element);
    }


    /**
     * Kuro sąnaudos
     */
    private function _addFuelConsumption() {
        $element = new Zend_Form_Element_Text('fuel_comsume_city');
        $element->setLabel('Kuro sąnaudos mieste');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('fuel_comsume_outside');
        $element->setLabel('Kuro sąnaudos magistralėje');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('fuel_comsume_all');
        $element->setLabel('Kuro sąnaudos mišriai');
        $this->addElement($element);
    }


    private function _addNotes() {
        $element_lt = new Zend_Form_Element_Textarea('notes_lt');
        $element_ru = new Zend_Form_Element_Textarea('notes_ru');
        $element_en = new Zend_Form_Element_Textarea('notes_en');
        $element_lt->setLabel('Komentaras LT');
        $element_ru->setLabel('Komentaras RU');
        $element_en->setLabel('Komentaras EN');
        $this->addElement($element_lt);
        $this->addElement($element_ru);
        $this->addElement($element_en);
    }


    private function _addContacts() {
        $model = new App_Model_Table_Auto_Contacts();
        $options = $model->getOptions();

        $element = new Zend_Form_Element_Select('contacts');
        $element->setMultiOptions($options)->setLabel('Kontaktai');
        $this->addElement($element);
    }


    private function _addOptions() {
        $subform = new Zend_Form_SubForm();
        $model = new App_Model_Table_Auto_Options();
        $options = $model->getOptionsBySlug('options', false);


        foreach ($options as $key => $row) {
            $element = new Zend_Form_Element_Checkbox('opt_' . $key);
            $element->setLabel($row)
                ->setOptions(
                array(
                'checkedValue' => $key
                )
            );
            $subform->addElement($element);
        }

        $this->addSubForm($subform, 'options');
    }


    /**
     * Rida
     */
    private function _addMileage() {
        $element = new Zend_Form_Element_Text('mileage');
        $element->setLabel('Rida')
            ->addValidator('Digits')
            ->setRequired(true);

        $this->addElement($element);
    }


    /**
     * Paruošia duomenis, ateinančius iš formos, įterpimui į db.
     * @param array $data
     * @return array
     */
    public function prepareValues($data) {
        if (isset($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $key => $row) {
                if ($row == 0) {
                    unset($data['options'][ $key ]);
                }
            }
            $data['options'] = implode(',', $data['options']);
        }

        if (isset($data['id']) && $data['id'] == "") {
            unset($data['id']);
        }

        return $data;
    }


    public function save() {
        $model = new App_Model_Table_Auto();
        $values = $this->prepareValues($this->getValues());

        if ($this->getValue('id') > 0) {
            $rowset = $model->find($this->getValue('id'));
            if ($rowset->count() > 0) {
                $row = $rowset->current();
                $row->setFromArray($values);
                $row->modified = new Zend_Db_Expr('NOW()');
                $row->save();
                return true;
            }
            return false;
        }
        else {
            $row = $model->createRow($values);
            $row->created = new Zend_Db_Expr('NOW()');
            $row->modified = new Zend_Db_Expr('NOW()');
            $row->save();
            return true;
        }
    }
}
?>
