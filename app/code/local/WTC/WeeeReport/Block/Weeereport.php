<?php
 class WTC_WeeeReport_Block_Weeereport extends Mage_Core_Block_Template {

public function _prepareLayout() {
    return parent::_prepareLayout();
}

public function getMymodule() {
    if (!$this->hasData('weeereport')) {
        $this->setData('weeereport', Mage::registry('weeereport'));
    }
    return $this->getData('weeereport');
} 
}