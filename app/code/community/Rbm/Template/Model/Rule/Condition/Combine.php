<?php

class Rbm_Template_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{

    public function __construct()
    {
        parent::__construct();
        $this->setType('rbmTemplate/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $typeConditions = $this->getRule()->getTypeConditions();
        foreach($typeConditions as $conditionLabel =>  $newCondition){
            $newAttributes = $newCondition->loadAttributeOptions()->getAttributeOption();            
            $attributes = array();                        
            foreach ($newAttributes as $code => $label) {
                $attributes[] = array('value' => "{$newCondition->getType()}|{$code}",
                    'label' => $label);
            }

            $conditions = array_merge_recursive($conditions,
                    array(    
                        array('value' => 'rbmTemplate/rule_condition_combine', 'label' => Mage::helper('rbmTemplate')->__('Conditions Combination')),
                        array('label' => $conditionLabel,
                        'value' => $attributes),
            ));
        }
        return $conditions;
    }
    
    
    public function collectValidatedAttributes($typeCollection)
    {
        foreach($this->getConditions() as $condition){
            $condition->collectValidatedAttributes($typeCollection);
        }
        return $this;
        
    }
    
//    public function getFilterConditionsForCollection($collection){
//        $this->filterCollection($collection);
//        return null;        
//    }
//
//    public function filterCollection(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
//    {
//        /*@var $collection Mage_Catalog_Model_Resource_Product_Collection*/        
//        
//        $value = $this->getValue(); // case 1 we use normal operator, case 0 we use NOT operator;        
//        switch ($this->getAggregator()) {
//            case 'all':
//                $select = $collection->getSelect();
//                foreach ($this->getConditions() as $condition) {
//                    $primaryWhere = $select->getPart(Varien_Db_Select::WHERE);
//                    $primaryWhere = join(' ',$primaryWhere);
//                    $select->reset(Varien_Db_Select::WHERE);  
//                    
//                    $filter = $condition->getFilterConditionsForCollection($collection);
//                    if($filter){
//                        $collection->addAttributeToFilter($filter['attribute'],
//                                array(
//                            $filter['operator'] => $filter['value']
//                        ));
//                    }
//                    
//                    
//                    $secondaryWhere = $select->getPart(Varien_Db_Select::WHERE);
//                    $secondaryWhere = join(' ',$secondaryWhere);
//                    $select->reset(Varien_Db_Select::WHERE);
//                    if(!$value){
//                        $secondaryWhere = " NOT ({$secondaryWhere} )";
//                    }else{
//                        $secondaryWhere = " ({$secondaryWhere} )";
//                    }
//                    if(trim($primaryWhere) !== ''){                                                
//                        $select->where($primaryWhere);
//                    }
//                    $select->where($secondaryWhere);
//                    
//                }                
//                
//                
//                
//                //we use and conditions
//                break;
//            case 'any':                
//                $conditions = array();
//                $combinedCondtitions = array();
//                
//                foreach ($this->getConditions() as $condition) {
//                    if($condition instanceof self){
//                       $combinedCondtitions[] =  $condition;                       
//                    }else{
//                        $filter = $condition->getFilterConditionsForCollection($collection);
//                        $conditions[] = array(
//                                'attribute' => $filter['attribute'],
//                                $filter['operator'] => $filter['value']                        
//                        );
//                        
//                    }
//                }                
//                $select = $collection->getSelect();
//                $primaryWhere = $select->getPart(Varien_Db_Select::WHERE);
//                $primaryWhere = join(' ',$primaryWhere);
//                $select->reset(Varien_Db_Select::WHERE);                
//                $collection->addAttributeToFilter($conditions);
//                
//                
//                
//                $secondaryWhere = $select->getPart(Varien_Db_Select::WHERE);
//                $secondaryWhere = join(' ',$secondaryWhere);
//                $select->reset(Varien_Db_Select::WHERE);
//                if(!$value){
//                    $secondaryWhere = " NOT ({$secondaryWhere} )";
//                }else{
//                    $secondaryWhere = " ({$secondaryWhere} )";
//                }
//                
//                if(trim($primaryWhere) !== ''){
//                    $select->where($primaryWhere);
//                }
//                $select->orWhere($secondaryWhere);
//                
//                
//                //second loop over combined condition because we need add simple filters first
//                foreach($combinedCondtitions as $condition){
//                    $condition->getFilterConditionsForCollection($collection,$this->getAggregator(),$value);
//                }
//                
//                //we use or conditions
//                    break;
//        }
//    }

}
