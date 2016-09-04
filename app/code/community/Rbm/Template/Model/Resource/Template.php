<?php
class Rbm_Template_Model_Resource_Template extends Mage_Core_Model_Resource_Db_Abstract{
    public function _construct()
    {        
        $this->_init('rbmTemplate/template','template_id');
    }
    
    protected function _beforeSave(\Mage_Core_Model_Abstract $object)
    {
        if(trim($object->getCode()) === ''){
            Mage::throwException("Code is required");
        }
        parent::_beforeSave($object);
    }
    
}