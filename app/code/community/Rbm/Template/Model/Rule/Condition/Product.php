<?php

class Rbm_Template_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{


    public function __construct()
    {
        parent::__construct();
        $this->setType('rbmTemplate/rule_condition_product');
    }

    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);

        // $attributes['status'] = Mage::helper('rbmTemplate')->__('Status');
        $attributes['type_id'] = Mage::helper('rbmTemplate')->__('Product Type');
    }

    protected function _prepareValueOptions()
    {
        $result = null;
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }
        $selectOptions = null;
        if ($this->getAttribute() === 'status') {
            $selectOptions = Mage_Catalog_Model_Product_Status::getOptionArray();
        } elseif ($this->getAttribute() === 'type_id') {
            $selectOptions = Mage_Catalog_Model_Product_Type::getAllOptions();
        } else {
            return parent::_prepareValueOptions();
        }
        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    public function getValueElementType()
    {

        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getValueElementType();
    }

//    public function getFilterConditionsForCollection(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
//    {
//        $attribute = $this->getAttribute();
//        if('category_ids' == $attribute){
////            $attribute = 'category_id';
//
//            $select = $collection->getResource()->getReadConnection()->select()->distinct()
//            ->from($collection->getTable('catalog/category_product_index'), array('product_id'))
//            ->where('is_parent = 1 AND e.entity_id = product_id');
//
//
//            $select->where('category_id in (?)',$this->getValue());
//            switch($this->getOperator()){
//                case '()':
//                case '==':
//                    $collection->getSelect()->where("exists( {$select})");
//                    break;
//                case '!()':
//                case '!=':
//                    $collection->getSelect()->where("NOT exists( {$select})");
//                    break;
//            }
//
//            return null;
//        }
//        $result = array(
//            'attribute' => $attribute,
//            'operator' => $this->_translateOperator($this->getOperator()),
//            'value' => $this->getValue()
//        );
//        return $result;
//    }
//
//
//
//    protected function _translateOperator($operator)
//    {
//
//        $tranlateTable = array(
//            '==' => 'eq',
//            '!=' => 'neq',
//            '{}' => 'like',
//            '!{}' => 'nlike',
//            '()' => 'in',
//            '!()' => 'nin',
//            '>' => 'gt',
//            '<' => 'lt',
//            '>=' => 'gteq',
//            '<=' => 'lteq',
//        );
//        return $tranlateTable[$operator];
//    }

}
