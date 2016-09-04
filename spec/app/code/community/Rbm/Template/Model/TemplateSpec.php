<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class Rbm_Template_Model_TemplateSpec extends ObjectBehavior
{
    function let(){     
        $this->beAnInstanceOf('Rbm_Template_Model_Template');        
    }
    
    function it_is_code_required(){
        $this->setData('code',null)->shouldThrow()->during('save');        
    }
    function it_can_save_with_valid_code(){
        $this->setData('code','xyz123')->save()->getId()->shouldNotReturn(null);        
        $this->loadByCode('xyz123')->getId()->shouldNotReturn(null);
    }    
    function it_return_product_collection_on_get_type_collection_when_type_is_null(){
        $this->setData('type',  null)
            ->getTypeCollection()->shouldReturnAnInstanceOf('Mage_Catalog_Model_Resource_Product_Collection');
    }
    
    function it_return_product_collection_on_get_type_collection(){
        $this->setData('type',  \Rbm_Template_Model_Source_Template_Type::PRODUCT)
            ->getTypeCollection()->shouldReturnAnInstanceOf('Mage_Catalog_Model_Resource_Product_Collection');        
    }
     
    function it_is_initializable()
    {   
        $this->shouldHaveType('Rbm_Template_Model_Template');
    }
    function it_is_loadable()
    {   
//        echo get_class($this->getWrappedObject()->getResource()) . "\n";
        $this->loadByCode("default")->getId()->shouldReturn(null);
    }
}
