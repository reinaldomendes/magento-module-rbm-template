<?php
$installer = $this;
/*@var $installer Rbm_Template_Model_Resource_Setup*/
$installer->startSetup();
$conn = $installer->getConnection();
/*@var $conn Varien_Db_Adapter_Interface*/
$templateTableName = $installer->getTable('rbmTemplate/template');
$templateTable = $conn->newTable($templateTableName)
        ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')        
        ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT,null,array(        
            'nullable'  => false,        
        ))
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(        
            'nullable'  => false,        
        ))
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT,null,array(        
            'nullable'  => false,        
        ))
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT,32,array(        
            'nullable'  => false,        
        ))
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(        
            'nullable'  => false,        
        ))
        ->addColumn('mime_type', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(        
            'nullable'  => false,        
        ))
         ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(        
            'nullable'  => false,        
        ))
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(        
            'nullable'  => false,        
        ))
        ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT,null,array(        
            'nullable'  => true,        
        ))        
        ->addForeignKey($installer->getFkName('rbmTemplate/template', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        
        ->addIndex($indexName, array('code'),array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));        

$conn->dropTable($templateTableName);
$conn->createTable($templateTable);



$templateFiltersTableName = $installer->getTable('rbmTemplate/filters');

$fkPriField = $fkRefField = 'template_id';

$fkTemplateFiltersBelongsToTemplate = $conn->getForeignKeyName($templateFiltersTableName, $fkPriField, $templateTableName, $fkRefField);


//$templateFiltersTable = $conn->newTable($templateFiltersTableName)
//        ->addColumn('filter_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
//        'identity'  => true,
//        'unsigned'  => true,
//        'nullable'  => false,
//        'primary'   => true,
//        ), 'Entity ID')
//          ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
//        'identity'  => false,
//        'unsigned'  => true,
//        'nullable'  => false,        
//        ), 'Entity ID')  
//        ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT,null,array(        
//            'nullable'  => false,        
//        ),'serializable conditions')        
//        ->addForeignKey($fkTemplateFiltersBelongsToTemplate, $fkPriField, $templateTableName, $fkRefField);

$conn->dropTable($templateFiltersTableName);
//$conn->createTable($templateFiltersTable);




$installer->endSetup();