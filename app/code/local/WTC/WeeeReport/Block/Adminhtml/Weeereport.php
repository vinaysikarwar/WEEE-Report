<?php  
ini_set("display_errors",1);
ini_set('max_execution_time',999999999);
class WTC_WeeeReport_Block_Adminhtml_Weeereport extends Mage_Adminhtml_Block_Template {

	public function __construct(){
		
	}
	
	protected function getCollection()
    {
		$filter = $this->getRequest()->getParam('filter'); 
		if($filter){
			$filter = base64_decode($filter);
			parse_str(urldecode($filter), $data);
			//echo '<pre>';print_r($data);die;
			$date = Mage::getSingleton('core/date');	
			
			if(!isset($data['store']) || $data['store'] == "all"){
				$storeIds = $this->getStores();
			}
			else{
				$storeIds = $data['store'];
				$storeIds = explode(",",$storeIds);
			}
			
			
			
			
			$collection = Mage::getResourceModel('sales/order_collection')
			
			->addFieldToFilter('store_id', array('in' => $storeIds))
			//->setPageSize(20)
			->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));
			
			if(isset($data['country'])){
				$country = $data['country'];
				$collection = $collection
									->join(array('a' => 'sales/order_address'), 'main_table.entity_id = a.parent_id AND a.address_type != \'billing\'', array(
										'country_id' => 'country_id'
									))
									//->setPageSize(20)
									->addAttributeToFilter('country_id', $country);
			}
			
			if($data['report_from'] != "" && $data['report_to'] != null){
				$formatDate = 'Y-m-d H:i:s';
				$from_date =date('Y-m-d H:i:s', strtotime($data['report_from'])); 
				$to_date = date('Y-m-d H:i:s', strtotime($data['report_to']));
				$collection = $collection->addAttributeToFilter('created_at', array(
								'from' => $from_date,
								'to' => $to_date,
								'date' => true,
							));
			}
			
			
			$collection->getSelect()->group('entity_id');
			
			$collection = $collection->load();
			return $collection;
		}
    }
	
	public function getItems(){
		$collection = $this->getCollection();
		if(count($collection) > 1) { 
			$global_data_arr = array();
			foreach($collection as $order)
			{
				$qty=0;
				$id= $order->getIncrementId();
				foreach($order->getAllVisibleItems() as $item)
				{
					$temp_data_arr = array();	
					$temp_data_arr['Sku'] = $item->getSku();	 
					$prod = Mage::getModel('catalog/product')->load($item->getProductId());	 
					if($prod->getWeeeProduct()){
						$temp_data_arr['Product_Name'] = $prod->getName();
						$store= Mage::getModel('core/store')->load($order->getStoreId());	 
						$temp_data_arr['Shop'] = $store->getName();
						$ship=$order->getShippingAddress();	 		 
						if($ship)
						{ 
							$temp_data_arr['Country'] = $ship->getCountry();
						}
						else
						{
							$temp_data_arr['Country'] = 'N/A';
						}
						$temp_data_arr['Period'] = Mage::getModel('core/date')->date('Y',strtotime($item->getCreatedAt()));
						$temp_data_arr['Month'] = Mage::getModel('core/date')->date('F',strtotime($item->getCreatedAt()));
						$temp_data_arr['Amount'] = $item->getQtyOrdered();
						$temp_data_arr['Weight'] = $item->getWeight();
						$temp_data_arr['total_amount'] = $item->getQtyOrdered() * $item->getPrice();
						$temp_data_arr['total_weight'] = $item->getQtyOrdered() * $prod->getWeight();
						$temp_data_arr['created_at'] = $order->getCreatedAt();
						$temp_data_arr['store_name'] = $order->getStoreName();
						$found  = '0';
						for ($outer_counter = 0 ;  $outer_counter  <  sizeof($global_data_arr) ; $outer_counter++ )
						{			
							if($global_data_arr[$outer_counter]['Sku']   ==  $temp_data_arr['Sku']  && $global_data_arr[$outer_counter]['Shop']   ==  $temp_data_arr['Shop']  &&  $global_data_arr[$outer_counter]['Country']   ==  $temp_data_arr['Country']  )
							{
								//&& $global_data_arr[$outer_counter]['Shop']   ==  $temp_data_arr['Shop']  &&  $global_data_arr[$outer_counter]['Country']   ==  $temp_data_arr['Country'] 	
								$found = '1';
								$global_data_arr[$outer_counter]['Amount'] += 	$temp_data_arr['Amount'] ;					
								break;				
							}			
						}
						if(	$found == '0')
						{
							$global_data_arr[]  = $temp_data_arr;		
						}
					}
				}
			}
			return $global_data_arr;
		}
	}
	
	public function getFilterData(){
		$filter = $this->getRequest()->getParam('filter'); 
		$data =  array();
		if($filter){
			$filter = base64_decode($filter);
			parse_str(urldecode($filter), $data);
			$date = Mage::getSingleton('core/date');
		}
		return $data;
	}
	
	public function getProductTotalWeight($weight){
		if($weight != 0){
			return $this->__($weight." Kg");
		}else{
			return "-";
		}
	}
	
	public function getStores(){
		$coll = Mage::getModel('core/store')->getCollection();
		foreach($coll as $store){
			$storeId = $store->getId();
			$stores[] = $storeId;
		}
		//$storeIds = "'".implode("','",$stores)."'";
		return $stores;//implode(",",$stores);
	}
	
	public function getPdfExportUrl(){
		$this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/exportPdf', array('_current' => true));
	}
}