<?php
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'wee_product');

$data=array(
'type'=>'int',
'input'=>'boolean', //for Yes/No dropdown
'sort_order'=>50,
'label'=>'Weee Product',
'global'=>Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
'required'=>'0',
'comparable'=>'0',
'searchable'=>'0',
'is_configurable'=>'1',
'user_defined'=>'1',
'visible_on_front' => 0, //want to show on frontend?
'visible_in_advanced_search' => 0,
'is_html_allowed_on_front' => 0,
'required'=> 0,
'unique'=>false,
'apply_to' => 'simple,configurable,bundle,grouped,virtual,downloadable', //
'is_configurable' => false
);

$installer->addAttribute('catalog_product','weee_product',$data);

$installer->addAttributeToSet(
    'catalog_product', 'Default', 'General', 'weee_product'
); //Default = attribute set, General = attribute group

$installer->endSetup();