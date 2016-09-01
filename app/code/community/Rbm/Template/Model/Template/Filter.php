<?php

class Rbm_Template_Model_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{

    const CONSTRUCTION_FOREACH_PATTERN = '/{{foreach\s*(.*?)}}(.*?){{\\/foreach\s*}}/si';

    protected $_templateModel = null;
    protected $_collection = null;

    const CACHE_TAG = 'rbm_template_2';

    public function getCacheKey($value)
    {
        return join('_',
                array(
            self::CACHE_TAG,
            md5($value),
        ));
    }

    /**
     * max length of method prefix is 10
     * @return string
     */
    public function descriptioDirective()
    {

        return $this->getTemplateModel()->getData('description');
    }

    public function urlDirective()
    {
        return $this->getTemplateModel()->getUrl();
    }

    public function titleDirective()
    {


        return $this->getTemplateModel()->getData('title');
    }

    public function foreachDirective($construction)
    {

        $vars = preg_split('@\s+@', $construction[1]);
        $collectionVariable = null;
        $params = null;
        foreach ($vars as $value) {

            if (null === $collectionVariable) {
                if (strpos($value, '$') === 0) {
                    $value = substr($value, 1);
                }
                $collectionVariable = $this->_getVariable($value, '');
            } else if (strpos($value, '=') !== false) {
                $paramValue = current($this->_getBlockParameters($value));
                $paramKey = current(explode('=', $value));
                $params[$paramKey] = $paramValue;
            }
        }

        $foreachTemplate = $construction[2];

        $bkpTemplateVars = $this->_templateVars;
        $result = '';
        Varien_Profiler::enable();
        
        
        $matchIds = $this->getTemplateModel()->getMatchingTypeIds();
        
        
        
        $chunks = array_chunk($matchIds, 100);
        $baseCollectionVariable = $collectionVariable;
        
        foreach ($chunks as $chunk) {
            $collectionVariable = clone $baseCollectionVariable;            
            $collectionVariable->addIdFilter($chunk);            
            foreach ($collectionVariable as $item) {
                /* @var $item Mage_Catalog_Model_Category */
                if ($params['as']) {
                    $this->setVariables(array($params['as'] => $item) + $this->_templateVars);
                }
                $result .= parent::filter($foreachTemplate);
                unset($item);
            }
        }        
        $this->setVariables($bkpTemplateVars);
        return $result;
    }

    public function helperDirective($construction)
    {
        $helperParameters = $this->_getIncludeParameters($construction[2]);
        $helper = $helperParameters['type'];
        unset($helperParameters['type']);
        $arrayHelper = explode('/', $helper);
        $helperMethod = array_pop($arrayHelper);
        $helperClass = join('/', $arrayHelper);

        $helperObject = Mage::helper($helperClass);

        $result = call_user_func_array(array($helperObject, $helperMethod),
                array_values($helperParameters));

        $parts = explode('|', $construction[2], 2);
        if (2 === count($parts)) {
            list($variableName, $modifiersString) = $parts;

            $result = $this->_amplifyModifiers($result, $modifiersString);
        }

        return $result;
    }

    /**
     * 
     * @return Rbm_Template_Model_Template
     */
    public function getTemplateModel()
    {
        return $this->_templateModel;
    }

    /**
     * 
     * @param Rbm_Template_Model_Template $templateModel
     * @return \Rbm_Template_Model_Template_Filter
     */
    public function setTemplateModel(Rbm_Template_Model_Template $templateModel)
    {
        $this->_templateModel = $templateModel;
        $this->setVariables(array('collection' => $templateModel->getTypeCollection()));
        return $this;
    }

    public function modifierEscape($value, $type = 'html')
    {
        switch ($type) {
            case 'xml':
                return htmlspecialchars($value, ENT_XML1, 'UTF-8');
        }
        return parent::modifierEscape($value);
    }

    /**
     * 
     * @param string $code - rbm template code
     * @return string
     */
    public function filter($value)
    {

        $id = $this->getTemplateModel()->getId();
        ;
        $serializedConditions = $this->getTemplateModel()->getSerializedConditions();
        $keyPrepare = join('_',
                array($value, strlen($value), $id, $serializedConditions, strlen($serializedConditions)));
        $keyUpdate = md5($keyPrepare);
        $cacheKey = $this->getCacheKey($keyUpdate);


        $result = Mage::app()->getCache()->load($cacheKey);        
        if (!$result) {

            // "depend" and "if" operands should be first
            foreach (array(
        self::CONSTRUCTION_FOREACH_PATTERN => 'foreachDirective',
            ) as $pattern => $directive) {
                if (preg_match_all($pattern, $value, $constructions,
                                PREG_SET_ORDER)) {
                    foreach ($constructions as $index => $construction) {
                        $replacedValue = '';
                        $callback = array($this, $directive);
                        if (!is_callable($callback)) {
                            continue;
                        }
                        try {
                            $replacedValue = call_user_func($callback,
                                    $construction);
                        } catch (Exception $e) {
                            throw $e;
                        }
                        $value = str_replace($construction[0], $replacedValue,
                                $value);
                    }
                }
            }

            $result = parent::filter($value);
            Mage::app()->getCache()->save($result, $cacheKey,
                    array(self::CACHE_TAG), 600);
        }
        return $result;
    }

}
