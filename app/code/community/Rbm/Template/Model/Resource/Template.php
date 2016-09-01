<?php
class Rbm_Template_Model_Resource_Template extends Mage_Core_Model_Resource_Db_Abstract{
    public function _construct()
    {        
        $this->_init('rbmTemplate/template','template_id');
    }
    
}