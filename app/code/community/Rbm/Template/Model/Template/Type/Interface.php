<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rbm_Template_Model_Template_Type_Abstract
 *
 * @author reinaldo
 */
interface Rbm_Template_Model_Template_Type_Interface
{

    public function getTypeConditions();

    public function getTypeCollection();
    
    public function getModel();
}
