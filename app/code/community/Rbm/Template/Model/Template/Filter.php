<?php

class Rbm_Template_Model_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{

    const CONSTRUCTION_FOREACH_PATTERN = '/{{foreach\s*(.*?)}}(.*?){{\\/foreach\s*}}/si';

    protected $_templateModel = null;
    protected $_collection = null;

    const CACHE_TAG = 'rbm_template';
    
    const CACHE_DIR = 'rbm-template';
    
    protected $_useTmpDir = false;
    
    
    
    public function __construct()
    {
        parent::__construct();
        $this->_modifiers['price'] = array($this, 'modifierPrice');
        $this->_modifiers['striptags'] = array($this, 'modifierStriptags');
    }
    
    public function useTmpDir($flag=true){
        $this->_useTmpDir = $flag;
    }
    
    public function getCacheDir(){
        $tmpDir = $this->_useTmpDir ? DS . "tmp" : '';
        
        $dir = Mage::getBaseDir('var'). $tmpDir .  DS . self::CACHE_DIR;
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        return $dir;
    }
    public function getCacheFile($value){
        return $this->getCacheDir() . DS . $this->getCacheKey($value);
    }

    public function getCacheKey($value)
    {

        $id = $this->getTemplateModel()->getId();
        $serializedConditions = $this->getTemplateModel()->getSerializedConditions();
        $data = $this->getTemplateModel()->getData();

        
        foreach($data as $k => $v){
            if(is_object($v)){
                $data[$k] = md5(serialize($v));
                continue;
            }             
            $data[$k]  = md5($v);
        }
        $data['version'] = '1.1';
//        $data['protocol'] = Mage::app()->getRequest()->isSecure() ? 'https' : 'http';
        $data['store_id'] = $this->getTemplateModel()->getStore()->getId();
        
        $data = array_filter($data);
        ksort($data);
        $preKeyPrepare = join(',',$data);
        
        
        $keyPrepare = $preKeyPrepare . join('_',
                array($value, strlen($value), $id, $serializedConditions, strlen($serializedConditions)));
        $keyUpdate = md5($keyPrepare);
        
        

        return join('_',
                array(
            self::CACHE_TAG,
            $this->getTemplateModel()->getStore()->getCode(),
            $this->getTemplateModel()->getCode(),
            $keyUpdate,
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
        

        $matchIds = $this->getTemplateModel()->getMatchingTypeIds();
        
             
//        $file = $this->getCacheDir() . DS . 'include_' . $this->getTemplateModel()->getCode() . '_' . md5(time());        
//        $pFile = fopen($file, 'wb');


        $chunks = array_chunk($matchIds, 100);
        $baseCollectionVariable = $collectionVariable;
        $result = '';
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
            
//            fwrite($pFile,$result);
//            fflush($pFile);
        }        
//        fclose($pFile);
        $this->setVariables($bkpTemplateVars);
        
        
        return $result  ;
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
    
    public function modifierPrice($value){
        return number_format($value, 2, '.', '');
    }
    
    public function modifierStriptags($value){
        return strip_tags($value);
    }

    /**
     *
     * @param string $code - rbm template code
     * @return string
     */
    public function filter($value)
    {        
        $file = $this->getCacheFile($value);
        
        
        if(is_file($file)){                 
            return file_get_contents($file);
        }
        

        $lockFile = Mage::getBaseDir('var') . DS . 'locks' . DS . "rbm-template-{$this->getTemplateModel()->getId()}.lock";
        $p = fopen($lockFile,'w');
        $count = 0;
        while(!flock($p,LOCK_EX|LOCK_NB)){            
            usleep(1000 * 100);
            $count++;
            if($count > 10) {
                throw new Exception('Process is locked for ' . $lockFile);
            }
        }


        
       
        // "depend" and "if" operands should be first
        $paternsDirective = array( self::CONSTRUCTION_FOREACH_PATTERN => 'foreachDirective');
        foreach ($paternsDirective as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $index => $construction) {
                    $replacedValue = '';
                    
                    $callback = array($this, $directive);
                    if (!is_callable($callback)) {
                        continue;
                    }
                    
                    try {
                        $replacedValue = call_user_func($callback, $construction);                                                
//                        $replacedValue = "\n#include '{$replacedValue}'\n";
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        $result = parent::filter($value);
        file_put_contents($file,$result);
        /*
        $array = preg_split('@\n@',$result);        
        unset($result);
        $oldIgnoreUserAbort = ignore_user_abort();
        ignore_user_abort(true);
        $pFile = fopen($file,'wb');
        foreach($array as $line){
            if(false !== strpos($line,'#include')){
                $fileInclude = trim(trim(str_replace('#include','',$line)),"'");                
                $pInclude = fopen($fileInclude,'rb');
                while(!feof($pInclude)){
                    fwrite($pFile,fread($pInclude,1024));
                }
                fclose($pInclude); 
                unlink($fileInclude);
            }
            fwrite($pFile, $line);
        }
        fclose($pFile); 
        unset($array);
        
        flock($p,LOCK_UN);
        fclose($p);
        ignore_user_abort($oldIgnoreUserAbort);
         * 
         */

        return $result;
    }

}
