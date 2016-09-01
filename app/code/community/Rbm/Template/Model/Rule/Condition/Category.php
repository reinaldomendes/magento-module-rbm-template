<?php

class Rbm_Template_Model_Rule_Condition_Category extends Mage_Rule_Model_Condition_Product_Abstract
{

    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';
    public function __construct()
    {
        parent::__construct();
        $this->setType('rbmTemplate/rule_condition_category');
    }

     public function getAttributeObject()
    {
         
         $this->getAttribute();
        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Category::ENTITY, $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('catalog/category'))
                ->setFrontendInput('text');
        }
        return $obj;
    }
    
    protected function _addSpecialAttributes(array &$attributes)
    {
//        parent::_addSpecialAttributes($attributes);

          $attributes['parent_id'] = Mage::helper('rbmTemplate')->__('Parent Category');
//        $attributes['type_id'] = Mage::helper('rbmTemplate')->__('Product Type');
    }
    
    
    
    public function loadAttributeOptions()
    {
        $categoryAttributes = Mage::getResourceSingleton('catalog/category')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($categoryAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            
            if (!$attribute->isAllowedForRuleCondition() || !$attribute->getDataUsingMethod($this->_isUsedForRuleProperty)
            ) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        
        

        $this->_addSpecialAttributes($attributes);        

        asort($attributes);
        $this->setAttributeOption($attributes);        

        return $this;
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
        
        return parent::_prepareValueOptions();        
//        // Set new values only if we really got them
//        if ($selectOptions !== null) {
//            // Overwrite only not already existing values
//            if (!$selectReady) {
//                $this->setData('value_select_options', $selectOptions);
//            }
//            if (!$hashedReady) {
//                $hashedOptions = array();
//                foreach ($selectOptions as $o) {
//                    if (is_array($o['value'])) {
//                        continue; // We cannot use array as index
//                    }
//                    $hashedOptions[$o['value']] = $o['label'];
//                }
//                $this->setData('value_option', $hashedOptions);
//            }
//        }

        return $this;
    }

     public function getValueElementChooserUrl()
    {
         
         
         
        $url = false;        
        switch ($this->getAttribute()) {            
             case 'parent_id':
                $url = 'adminhtml/promo_widget/chooser'
                    .'/attribute/category_ids';
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
                break;
             default: 
                 parent::getValueElementChooserUrl();
        }        
        return $url!==false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }
    
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'parent_id':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger" title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }
        return $html;
    }
    
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'parent_id':
                return true;
        }
        
        return parent::getExplicitApply();
    }
    
    public function getInputType()
    {
       
        if ($this->getAttributeObject()->getAttributeCode() == 'parent_id') {
            return 'category';
        }

        return parent::getInputType();
    }

    
    
    public function collectValidatedAttributes($typeCollection)
    {
        $attribute = $this->getAttribute();
//        if ('parent_id' != $attribute) {
            if ($this->getAttributeObject()->isScopeGlobal()) {
                $attributes = $this->getRule()->getCollectedAttributes();
                $attributes[$attribute] = true;
                $this->getRule()->setCollectedAttributes($attributes);
                $typeCollection->addAttributeToSelect($attribute, 'left');
            } else {
                $this->_entityAttributeValues = $typeCollection->getAllAttributeValues($attribute);
            }
//        }

        return $this;
    }
    
    public function validate(Varien_Object $object)
    {
        switch ($this->getAttribute()) {
            case 'parent_id':
                return $this->validateAttribute(array($object->getParentId()));
        }        
        parent::validate($object);
    }

}
