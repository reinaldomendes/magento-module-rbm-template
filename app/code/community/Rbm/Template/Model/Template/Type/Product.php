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
class Rbm_Template_Model_Template_Type_Product implements Rbm_Template_Model_Template_Type_Interface
{

    /**
     * 
     * @return array(label => RbmTemplate_Model_Rule_Condition_Product)
     */
    public function getTypeConditions()
    {
        $productCondition = Mage::getModel('rbmTemplate/rule_condition_product');
        return array(Mage::helper('rbmTemplate')->__('Product Attributes') => $productCondition);
    }

    /**
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getTypeCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection->addStoreFilter(Mage::app()->getStore());
//                ->addAttributeToFilter('status', array('eq' => 1));
                
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);

        return $collection;
    }

    public function getModel()
    {
        return Mage::getModel('catalog/product');        
    }

}
