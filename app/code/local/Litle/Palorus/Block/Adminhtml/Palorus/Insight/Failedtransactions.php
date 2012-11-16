<?php
/**
* Litle Palorus Module
*
* NOTICE OF LICENSE
*
* Copyright (c) 2012 Litle & Co.
*
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*
* @category   Litle
* @package    Litle_Palorus
* @copyright  Copyright (c) 2012 Litle & Co.
* @license    http://www.opensource.org/licenses/mit-license.php
* @author     Litle & Co <sdksupport@litle.com> www.litle.com/developers
*/
class Litle_Palorus_Block_Adminhtml_Palorus_Insight_Failedtransactions
extends Mage_Adminhtml_Block_Widget_Grid
implements Mage_Adminhtml_Block_Widget_Tab_Interface {

	/**
	 * Set the template for the block
	 *
	 */
	
	public function _construct()
	{
		$this->setId('litle_customer_orders_grid');
		$this->setDefaultSort('failed_transactions_id', 'desc');
		$this->setUseAjax(true);
		$this->setFilterVisibility(true);
		$this->setPagerVisibility(true);
		parent::_construct();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('failed_transactions_id', array(
	                'header'    => 'Failed Transactions ID',
	                'width'     => '100',
	                'index'     => 'failed_transactions_id',
	                'sortable'		=> false,
		));

		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('palorus/adminhtml_myform/failedtransactionsview/', array('failed_transactions_id' => $row->getFailedTransactionsId()));
	}
	
	
	protected function _prepareCollection()
	{
		$customerId = Mage::registry('current_customer')->getId();
		$collection = Mage::getModel('palorus/subscription')
			->getCollection()
			->addFieldToFilter('customer_id',$customerId);
		foreach ($collection as $order){
	 		$productId = $order->getData();
	 		$productName = $productId['product_id'];
 			$product = Mage::getModel('catalog/product')->load($productName);
 			$name = $product->getName();
			$order->setData('name', $name);
			$amount = money_format('%i', $productId['amount']/100);
			$order->setData('price', '$'.$amount);
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Retrieve the label used for the tab relating to this block
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return $this->__('Litle & Co. Subscription');
	}

	/**
	 * Retrieve the title used by this tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->__('Click here to view Litle & Co. Subscription');
	}

	/**
	 * Determines whether to display the tab
	 * Add logic here to decide whether you want the tab to display
	 *
	 * @return bool
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Stops the tab being hidden
	 *
	 * @return bool
	 */
	public function isHidden()
	{
		return false;
	}


}