<?php

class Rbm_Template_Model_Source_Template_Type{
    const PRODUCT = 'catalog/product';
    const CATEGORY = 'catalog/category';
    public function toOptionArray(){
        return array(
            array(
                'label' => "Please Select",
                'value' => null,
            ),
            array(
                'label' => "Product Collection",
                'value' => self::PRODUCT                
            ),
            array(
                'label' => "Category Collection",
                'value' => self::CATEGORY                
            )
        );
    }
}