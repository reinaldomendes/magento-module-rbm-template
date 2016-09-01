<?php
class Rbm_Template_Helper_Product extends Mage_Core_Helper_Abstract{
    public function getCategoryFullPath(Mage_Catalog_Model_Product $product,$separator = '&gt;'){
       $categoryCollection = $product->getCategoryCollection()->addOrder('level','desc')->addAttributeToSelect('name')->load();
       $firstCategory = $categoryCollection->getFirstItem();
       /*@var $firstCategory Mage_Catalog_Model_Category*/
       $result = array();
       $auxCategory = $firstCategory;
       do{
          $result[] = $auxCategory->getName(); 
          $currentCategory = $auxCategory;
       }while(($auxCategory = $auxCategory->getParentCategory() && $currentCategory->getId() != $auxCategory->getId()));
              
       $separatorDecoded = html_entity_decode($separator);               
       return join(htmlentities($separatorDecoded), array_reverse($result));
    }
    public function joinCategory(Mage_Catalog_Model_Product $product){
        return join(",", $product->getAvailableInCategories());
    }
}