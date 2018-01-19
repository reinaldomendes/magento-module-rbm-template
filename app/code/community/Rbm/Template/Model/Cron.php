<?php

class Rbm_Template_Model_Cron
{

    const TMP_TEMPLATE_DIR = 'var/tmp/feed';

    public function cacheAllFeeds($data)
    {
        $lockFile = Mage::getBaseDir('var') . DS . 'locks' . DS . 'rbm-template-cron.lock';
        $p = fopen($lockFile, 'w');
        if (flock($p, LOCK_EX | LOCK_NB)) {

            /* Cleanup tmpdir */
            $templateFilter = Mage::getSingleton('rbmTemplate/template_filter');
            $templateFilter->useTmpDir(true);
            $tmpCacheDir = $templateFilter->getCacheDir();
            $files = glob("{$templateFilter->getCacheDir()}/*");
            foreach ($files as $file) {
                unlink($file);
            }

            foreach (Mage::app()->getStores() as $store) {
                $appEmulation = Mage::getSingleton('core/app_emulation');
                /* @var $appEmulation Mage_Core_Model_App_Emulation */
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store->getId());
                try {
//                unset($_SERVER['HTTPS']);
//                $this->_cacheForStore($store);
                    $_SERVER['HTTPS'] = 'on';
                    $this->_cacheForStore($store);
                } catch (Exception $e) {
                    
                }
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }

            $templateFilter->useTmpDir(false);
            $definitiveCacheDir = $templateFilter->getCacheDir();
            $toRemoveDir = '/tmp/to-remove/';
            $cmd = [
                "mv '{$definitiveCacheDir}' '{$toRemoveDir}'",
                "mv '{$tmpCacheDir}' '{$definitiveCacheDir}'",
                "rm {$toRemoveDir}/* -Rf",
                "rmdir {$toRemoveDir}/"
            ];

            $cmd = join(';', $cmd);
            `{$cmd}`;

            flock($p, LOCK_UN);
        } else {
            
        }
        fclose($p);
    }

    protected function _cacheForStore($store)
    {
        $templateModelCollection = Mage::getResourceModel('rbmTemplate/template_collection')
                ->addStoreFilter($store)
                ->addFieldToFilter('is_active', array('eq' => 1));

        foreach ($templateModelCollection as $templateModel) {
            $templateModel->setStore($store);
            $filter = Mage::getModel('rbmTemplate/template_filter');
            $filter->useTmpDir(true);

            $filter->setTemplateModel($templateModel);
            $value = $templateModel->getContent();            
            
            $filter->filter($value);
            
        }
    }

}
