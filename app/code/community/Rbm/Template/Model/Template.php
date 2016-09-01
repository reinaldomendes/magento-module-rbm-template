<?php

class Rbm_Template_Model_Template extends Mage_Rule_Model_Rule
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    protected $_type_ids = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('rbmTemplate/template');
    }

    public function loadByCode($code)
    {
        return $this->load($code, 'code');
    }

    public function getUrl()
    {
        return Mage::getUrl('rt/index/view', array('code' => $this->getCode()));
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Rbm_Template_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('rbmTemplate/rule_condition_combine');
    }

    /**
     * Get rule condition product combine model instance
     * @unecessary
     * @return 
     */
    public function getActionsInstance()
    {
        return parent::getActionsInstance();
    }

    
    public function getTypeInstance(){
        $ary = explode('/',$this->getType());
        $type = end($ary);        
        if(!$type){
            $type = 'product';
        }
        
        return Mage::getModel("rbmTemplate/template_type_{$type}");
    }
    
    /**
     * 
     * @return array of type conditions
     */
    public function getTypeConditions(){
        return $this->getTypeInstance()->getTypeConditions();        
    }
    
    /**
     * 
     * @return Collection of type used
     */
    public function getTypeCollection()
    {   
        return $this->getTypeInstance()->getTypeCollection()//->addIdFilter($this->getMatchingTypeIds())
                ->addAttributeToSelect('*');
        
    }
    
     /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingTypeIds()
    {
        if (is_null($this->_type_ids)) {
            $this->_type_ids = array();
            $this->setCollectedAttributes(array());
            
                /** @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
            $typeCollection = $this->getTypeInstance()->getTypeCollection();                
            $this->getConditions()->collectValidatedAttributes($typeCollection);

            Mage::getSingleton('core/resource_iterator')->walk(
                $typeCollection->getSelect(),
                array(array($this, 'callbackValidateType')),
                array(
                    'attributes' => $this->getCollectedAttributes(),
                    'model'    => $this->getTypeInstance()->getModel(),
                )
            );
            
        }

        return $this->_type_ids;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateType($args)
    {
        $typeModel = clone $args['model'];
        $typeModel->setData($args['row']);        

        if ($this->getConditions()->validate($typeModel)) {            
                $this->_type_ids[] = $typeModel->getId();
            
        }
    }
    
    
    

    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('rbmTemplate')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('rbmTemplate')->__('Disabled'),
        ));
        return $statuses->getData();
    }

    /**
     * 
     * @return array()
     */
    public function getAvailableTypes()
    {
        $typeOptions = array();
        $typeOptionArray = Mage::getModel('rbmTemplate/source_template_type')->toOptionArray();
        foreach ($typeOptionArray as $v) {
            $typeOptions[$v['value']] = $v['label'];
        }
        return $typeOptions;
    }

}
