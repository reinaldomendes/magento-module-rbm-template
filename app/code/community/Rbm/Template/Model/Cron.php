<?php

class Rbm_Template_Model_Cron {
   public function cacheAllFeeds($data){
      $lockFile = Mage::getBaseDir('var') . DS . 'locks' . DS . 'rbm-template-cron.lock';
      $p = fopen($lockFile,'w');
      if(flock($p,LOCK_EX|LOCK_NB)){
         foreach(Mage::app()->getStores() as $store){
            $templateModelCollection = Mage::getResourceModel('rbmTemplate/template_collection')->addStoreFilter($store)
                        ->addFieldToFilter('is_active',array('eq' => 1));
            foreach($templateModelCollection as $templateModel){
               $templateModel->setStore($store);
               $filter = Mage::getModel('rbmTemplate/template_filter');
               $filter->setTemplateModel($templateModel);
               $value = $templateModel->getContent();
               $cacheKey = $filter->getCacheKey($value);

               $result = Mage::app()->getCache()->load($cacheKey);
               if(!$result){
                  $filter->filter($value);
               }
            }
         }
         flock($p,LOCK_UN);
      }else{

      }

      fclose($p);
   }


}
