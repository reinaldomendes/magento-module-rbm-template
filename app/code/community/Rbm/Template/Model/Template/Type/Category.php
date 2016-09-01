<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Product
 *
 * @author reinaldo
 */
class Rbm_Template_Model_Template_Type_Category
        implements Rbm_Template_Model_Template_Type_Interface
{

    /**
     * 
     * @return array(RbmTemplate_Model_Rule_Condition_Product)
     */
    public function getTypeConditions()
    {
        $categoryCondition = Mage::getModel('rbmTemplate/rule_condition_category');
        return array(Mage::helper('rbmTemplate')->__('Category Attributes') => $categoryCondition);
    }

    /**
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getTypeCollection()
    {


        $collection = Mage::getResourceModel('catalog/category_collection');
        /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection->setStore(Mage::app()->getStore())
                ->addAttributeToFilter('is_active', array('eq' => 1));

        

        return $collection;
    }
    
    public function getModel()
    {
        return Mage::getModel('catalog/category');        
    }

}
