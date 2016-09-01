<?php
class Rbm_Template_Adminhtml_RbmTemplate_TemplateController extends Mage_Adminhtml_Controller_Action{
    
     /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog/rbmTemplate')
            ->_addBreadcrumb(Mage::helper('rbmTemplate')->__('Catalog'), Mage::helper('rbmTemplate')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('rbmTemplate')->__('Manage Templates'), Mage::helper('rbmTemplate')->__('Manage Templates'))
        ;
        return $this;
    }

    public function indexAction(){
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Manage Templates'))
             ->_title($this->__('Manage Template'));

        $this->_initAction();
        $this->renderLayout();
    }
    
     /**
     * Create new CMS page
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS page
     */
    public function editAction()
    {
        $this->_title($this->__('Catalog'))
           ->_title($this->__('Manage Templates'))
             ->_title($this->__('Manage Template'));


        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('template_id');
        $model = Mage::getSingleton('rbmTemplate/template');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('rbmTemplate')->__('This template no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Template'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
//
//don't need this I using singleton model
//        // 4. Register model to use later in blocks
//        Mage::register('rbm_template_template', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('rbmTemplate')->__('Edit Template')
                    : Mage::helper('rbmTemplate')->__('New Template'),
                $id ? Mage::helper('rbmTemplate')->__('Edit Template')
                    : Mage::helper('rbmTemplate')->__('New Template'));

        $this->renderLayout();
    }
    
     public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {           
            //init model and set data
            $model = Mage::getModel('rbmTemplate/template');

            if ($id = $this->getRequest()->getParam('template_id')) {
                $model->load($id);
            }
            if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
            }
            unset($data['rule']);
            $model->loadPost($data);
            $session = Mage::getSingleton('adminhtml/session');
//            $model->setData($data);

            

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('template_id' => $model->getId(), '_current' => true));
                return;
            }

            // try to save it
            try {
                $session->setPageData($model->getData());
                // save the data
                $model->save();
                $session->setPageData(false);

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('rbmTemplate')->__('The template has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('template_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('rbmTemplate')->__('An error occurred while saving the template.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('template_id' => $this->getRequest()->getParam('template_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('template_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('rbmTemplate/template');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('rbmTemplate')->__('The template has been deleted.'));                
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {                
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('template_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a template to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
    
    protected function _validatePostData($data){
        return true;
    }
    
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('rbmTemplate/template'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    
}