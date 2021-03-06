<?php

class Rbm_Template_IndexController extends Mage_Core_Controller_Front_Action{

    public function viewAction(){
<<<<<<< HEAD
=======
        $timer = 0;
        set_time_limit($timer);
        ini_set('max_execution_time', $timer);
>>>>>>> cfb6053a51ce39b489e17d19b46d1fc76d034f48
        $code = $this->getRequest()->getParam('code');
        $templateModelCollection = Mage::getResourceModel('rbmTemplate/template_collection')->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('is_active',array('eq' => 1))
                ->addFieldToFilter('code',array('eq' => $code))->load();
        $templateModel = $templateModelCollection->getFirstItem();
        if($templateModel->getId()){
            $templateModel->setStore(Mage::app()->getStore());
            $filter = Mage::getModel('rbmTemplate/template_filter');
            $filter->setTemplateModel($templateModel);

            $value = $templateModel->getContent();
            $filteredValue = $filter->filter($value);

            $this->getResponse()->setBody($filteredValue)->setHeader('Content-Type', $templateModel->getMimeType(),true);

        }else{
            $this->_forward('defaultNoRoute');
        }




    }
}
