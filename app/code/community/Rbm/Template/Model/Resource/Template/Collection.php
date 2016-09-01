<?php

class Rbm_Template_Model_Resource_Template_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('rbmTemplate/template');
    }

    /**
     * Filter by store or stores
     * @param array|int|Mage_Core_Model_Store $stores 
     * @return \Rbm_Template_Model_Resource_Template_Collection
     */
    public function addStoreFilter($stores)
    {
        if ($stores instanceof Mage_Core_Model_Store) {
            $stores = $stores->getId();
        }
        if (!is_array($stores)) {
            $stores = (array) $stores;
        }        
        $stores[] = 0;
        $stores = array_unique($stores);
        $this->addFieldToFilter(
                array('store_id', 'store_id'),
                array(
                    array('in' => $stores),
                    array( 'null' => true)
                )
        );
        return $this;
    }

}
