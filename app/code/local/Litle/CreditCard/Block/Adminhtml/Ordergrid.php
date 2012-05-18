<?php 

class Litle_CreditCard_Block_Adminhtml_Ordergrid extends Mage_Adminhtml_Block_Sales_Order_Grid {
	
	protected function _prepareMassaction()
	{
	 parent::_prepareMassaction();
	 
			$this->getMassactionBlock()->addItem('Capture', array(
		                  'label' => Mage::helper('sales')->__('Capture'),
						   'url' 	  => $this->getUrl('creditcard/adminhtml_order/massCapture')
			));
	}
}