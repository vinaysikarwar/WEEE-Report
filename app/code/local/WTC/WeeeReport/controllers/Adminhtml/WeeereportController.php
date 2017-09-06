<?php
class WTC_WeeeReport_Adminhtml_WeeereportController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('weeereport/weeereportbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Wee Report"));
	   $this->renderLayout();
    }
}