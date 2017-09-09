<?php
require_once(Mage::getBaseDir().'/lib/tcpdf/tcpdf.php');
class WTC_WeeeReport_Adminhtml_WeeereportController extends Mage_Adminhtml_Controller_Report_Abstract
{
	
	 /**
     * Add report/products breadcrumbs
     *
     * @return Mage_Adminhtml_Report_ProductController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Products'), Mage::helper('reports')->__('Products'));
        return $this;
    }

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('weeereport/weeereportbackend');
		return true;
	}

	public function indexAction()
    {
		$block = $this->getLayout()->createBlock('weeereport/adminhtml_weeereport');
		$filter = $block->getFilterData();
		if($filter){
			$session = Mage::getSingleton('core/session');
			foreach($filter as $key => $value){
				if($key == "report_from"){
					$session->unsReportFrom($value);
					$session->setReportFrom($value);
				}elseif($key == "report_to"){
					$session->unsReportTo($value);
					$session->setReportTo($value);
				}
				elseif($key == "country"){
					$session->unsWeeCountry($value);
					$session->setWeeCountry($value);
				}elseif($key == "store"){
					$session->unsWeeeStore($value);
					$session->setWeeeStore($value);
				}
			}
		}
		
		$this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('Weee Reports'));

       // $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE, 'viewed');

        $this->_initAction()
            ->_setActiveMenu('report/weeereport')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Weee Product Report'), Mage::helper('adminhtml')->__('Weee Product Report'));

        $gridBlock = $this->getLayout()->getBlock('weeereport_block_adminhtml_report.grid');
        $filterFormBlock = $this->getLayout()->getBlock('weereport.grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
	
	public function getpdfCss(){
		$html = '<style type="text/css">/*
			 CSS-Tricks Example
			 by Chris Coyier
			 http://css-tricks.com
		*/

		* { margin: 0; padding: 0; }
		body { font: 14px/1.4 Georgia, serif; }
		#page-wrap { width: 800px; margin: 0 auto; }

		textarea { border: 0; font: 14px Georgia, Serif; overflow: hidden; resize: none; }
		table { border-collapse: collapse; }
		table td, table th {  padding: 5px; }

		#header { height: 15px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }

		#address { width: 250px; height: 150px; float: left; }
		#customer { overflow: hidden; }

		#logo { text-align: right; float: right; position: relative; margin-top: 25px; border: 1px solid #fff; max-width: 540px; max-height: 100px; overflow: hidden; }
		#logo:hover, #logo.edit { border: 1px solid #000; margin-top: 0px; max-height: 125px; }
		#logoctr { display: none; }
		#logo:hover #logoctr, #logo.edit #logoctr { display: block; text-align: right; line-height: 25px; background: #eee; padding: 0 5px; }
		#logohelp { text-align: left; display: none; font-style: italic; padding: 10px 5px;}
		#logohelp input { margin-bottom: 5px; }
		.edit #logohelp { display: block; }
		.edit #save-logo, .edit #cancel-logo { display: inline; }
		.edit #image, #save-logo, #cancel-logo, .edit #change-logo, .edit #delete-logo { display: none; }
		#customer-title { font-size: 20px; font-weight: bold; float: left; }

		#meta { margin-top: 1px; width: 300px; float: right; }
		#meta td { text-align: left;  }
		#meta td.meta-head { text-align: left; background: #eee; }
		#meta td textarea { width: 100%; height: 20px; text-align: right; }
		
		#address_filter_meta { margin-top: 10px; width: 100%; float: right; border:1px solid #dcdcdc;clear:both;margin-bottom:10px;}
		#address_filter_meta td { text-align: left;  }
		#address_filter_meta td.meta-head { text-align: left; background: #eee; }
		#address_filter_meta td textarea { width: 100%; height: 20px; text-align: right; }
		
		.filter_meta{float: left;width: 300px;border: 1px solid #000000;}
		.address_meta{float: right;width: 300px;border: 1px solid #000000;}
		

		#items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
		#items th { background: #eee; }
		#items textarea { width: 80px; height: 50px; }
		#items tr.item-row td { border: 0; vertical-align: top; }
		#items td.description { width: 300px; }
		#items td.item-name { width: 175px; }
		#items td.description textarea, #items td.item-name textarea { width: 100%; }
		#items td.total-line { border-right: 0; text-align: right; }
		#items td.total-value { border-left: 0; padding: 10px; }
		#items td.total-value textarea { height: 20px; background: none; }
		#items td.balance { background: #eee; }
		#items td.blank { border: 0; }

		#terms { text-align: center; margin: 20px 0 0 0; }
		#terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
		#terms textarea { width: 100%; text-align: center;}

		textarea:hover, textarea:focus, #items td.total-value textarea:hover, #items td.total-value textarea:focus, .delete:hover { background-color:#EEFF88; }

		.delete-wpr { position: relative; }
		.delete { display: block; color: #000; text-decoration: none; position: absolute; background: #EEEEEE; font-weight: bold; padding: 0px 3px; border: 1px solid; top: -6px; left: -22px; font-family: Verdana; font-size: 12px; }</style>';
		return $html;
	}
	
	public function getHtml($data,$custom){
		
		$block = $this->getLayout()->createBlock('weeereport/adminhtml_weeereport');
		$filter = $custom['filter'];
		$flt = "<strong>".$this->__("WEEE Report")."</strong>";
		$flt .= "<br/>".$this->__("Filters used as input")."<br/>";
		foreach($filter as $key => $value){
			$flt .= "$key = $value <br/>";
		}
		$image = '<img src="'.$custom['store']['logo'].'" />';
		$itemhtml = "";
		$totalWght = "";
		$totalQtyOrdered = "";
		foreach($data as $item){
			$weight = $item['total_weight'];
			$price = $item['total_amount'];
			$qtyOrdered = $item['Amount'];
			 $totalCost = $item['Amount'] * $item['total_amount'];
			 $totalWeight = $item['total_weight'] * $item['total_amount'];
			$itemhtml .= '
							<tr class="item-row">
							  <td class="item-name" style="border: 1px solid black;">
								'.$item['Sku'].'
							  </td>
							  <td class="item-name" style="border: 1px solid black;">
								'.$item['Amount'].'
							  </td>
							  
							  <td class="item-name" style="border: 1px solid black;">
								'. $block->getProductTotalWeight($totalWeight) .'
							  </td>
						  </tr>
			
						';
			$totalWght += $totalWeight;
			$totalQtyOrdered += $qtyOrdered;
		}
		$totalWeight = $block->getProductTotalWeight($totalWght);
		
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">

		<head>'.$this->getpdfCss().'
			
			<title>WEEE Report</title>

		</head>

		<body>
			<div id="page-wrap">
				<table id="meta" cellspacing="0" cellpadding="0" style="border: none;margin-bottom:20px;" border="0">
					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>'. $image.'</td>
					</tr>
					<tr class="filter_meta" style="border: 1px solid black">
						<td>'.$flt.'</td>
						<td>&nbsp;</td>
						<td>
							<strong style="margin-left:5px;">'.$this->__('Magento Store details').'</strong><br/>
							'.$custom['store']['name'].'<br/>
							'.$custom['store']['address'].'<br/>
							'.$custom['store']['phone'].'<br/>
						</td>
					</tr>
				</table>
				
				<table id="items" style="border: 1px solid black;margin-top:20px;">
				
				  <tr>
					  <th style="border: 1px solid black;">'.$this->__("Sku").'</th>
					  <th style="border: 1px solid black;">'.$this->__("Total Qty Ordered").'</th>
					  <th style="border: 1px solid black;">'.$this->__("Total Weight").'</th>
				  </tr>
				  '.$itemhtml.'
				  <tr>
					  <th style="border: 1px solid black;background:#dcdcdc;">'.$this->__("Total").'</th>
					  <th style="border: 1px solid black;background:#dcdcdc;">'.$totalQtyOrdered.'</th>
					  <th style="border: 1px solid black;background:#dcdcdc;">'.$totalWeight.'</th>
				  </tr>
				</table>
			</div>
			
		</body>

		</html>';
		return $html;
	}
	
	public function exportPdfAction(){
		$block = $this->getLayout()->createBlock('weeereport/adminhtml_weeereport');
		$filter = $block->getFilterData();
		$data = $block->getItems();
		$custom['store']['address'] = Mage::getStoreConfig('general/store_information/address');
		$custom['store']['name'] = Mage::getStoreConfig('general/store_information/name');
		$custom['store']['phone'] = Mage::getStoreConfig('general/store_information/phone');
		$baseUrl = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);
		$custom['store']['logo'] = $baseUrl.'media/sales/store/logo/'.Mage::getStoreConfig('sales/identity/logo');
		$custom['filter'] = $filter;
		if(null != $filter){
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			 // set document information
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);

			//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
			$pdf->SetMargins(10, 15); // Left, top
			// add a page
			$pdf->AddPage();
			
			$html = $this->getHtml($data,$custom);

			//$pdf->writeHTML($html, true, true, true, true, '');
			$pdf->writeHTML($html, true, 0, true, true);
			
			
			//Close and output PDF document
			/* $file_name_path = Mage::getBaseDir().'/var/pdf/order_invoice.pdf';
			if(file_exists($file_name_path))
			{
				unlink($file_name_path);
			} */
			//$pdf->Output($file_name_path, 'I');
			$filename = 'WEEE_Report.pdf';
			$pdf->Output($filename,'D');
			//@chmod($file_name_path, 0777);
			/*END:: To create pdf at particular location in server*/
			return $file_name;

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
	}
	
	public function generatePdf($couponIds)
	{
		
		$pdf_html = Mage::app()->getLayout()->createBlock('checkout/cart')->setTemplate('aoi/invoice.phtml')->toHtml();
		//echo $newBlock;
		$filename = Mage::getBaseDir()."/app/design/adminhtml/default/default/template/aoi/invoice.phtml"; 
		$logoImage = Mage::getBaseDir().'/skin/frontend/default/default/images/logo_pdf.jpg';
		$pdf_html = file_get_contents($filename);
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator('Vinay Sikarwar');
		$pdf->SetTitle('Amazon Order Invoice');
		$pdf->SetAuthor('Vinay Sikarwar');
		$pdf->SetKeywords('PDF','tcpdf','html');
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, ”, PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, ”, PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$lg = Array();
		$lg['a_meta_charset'] = 'UTF-8';
		$lg['a_meta_dir'] = 'ltr';
		$lg['a_meta_language'] = 'en';
		$lg['w_page'] = 'page';
		$pdf->SetFont('times',”,14); //set font of pdf and default font size is 20
		//set some language-dependent strings
		$pdf->setLanguageArray($lg);

		// add a page
		$pdf->AddPage();
		$pdf->writeHTML($pdf_html, true, 0, true, true);
		//$pdf->Output(‘test.pdf’,’D’); //this will create pdf and download at a time..
		/*START:: To create pdf at particular location in server*/
		$file_name = 'order_invoice.pdf';
		$file_name_path = Mage::getBaseDir().'/var/pdf/order_invoice.pdf';
		if(file_exists($file_name_path))
		{
			unlink($file_name_path);
		}
		//$pdf->Output($file_name_path,'I');
		$pdf->Output('order_invoice.pdf', 'I');
		//@chmod($file_name_path, 0777);
		/*END:: To create pdf at particular location in server*/
		//return $file_name;
	}
}