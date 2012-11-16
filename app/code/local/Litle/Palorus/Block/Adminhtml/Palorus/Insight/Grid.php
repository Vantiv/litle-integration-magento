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
class Litle_Palorus_Block_Adminhtml_Palorus_Insight_Grid
extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'palorus/failedtransactions_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('litle_txn_id', array(
            'header'=> Mage::helper('sales')->__('Litle Transaction Id'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'litle_txn_id',
        ));

        $this->addColumn('transaction_timestamp', array(
            'header' => Mage::helper('sales')->__('Date/Time'),
            'index' => 'transaction_timestamp',
            'type' => 'datetime',
            'width' => '140px',
        ));

        $this->addColumn('order_num', array(
            'header' => Mage::helper('sales')->__('Order Number'),
            'index' => 'order_num',
        ));

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('sales')->__('Customer Number'),
            'index' => 'customer_id',
        ));

        $this->addColumn('active', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'active',
            'type'  => 'options',
            'width' => '70px',
            'options' => array('Action Taken','Pending'),
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'palorus/adminhtml_myform/failedtransactionsview/'),
                        'field'   => 'failed_transactions_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('failed_transactions_id');
        $this->getMassactionBlock()->setUseSelectAll(false);

		$actionTakenUrl = $this->getUrl('*/adminhtml_myform/massFailedTransactionsMarkActionTaken');
        $this->getMassactionBlock()->addItem('mark_failed_transaction_inactive', array(
             'label'=> Mage::helper('sales')->__('Mark Action Taken'),
             'url'  => $actionTakenUrl,
        ));

		$actionNotTakenUrl = $this->getUrl('*/adminhtml_myform/massFailedTransactionsMarkActionNotTaken');
        $this->getMassactionBlock()->addItem('mark_failed_transaction_active', array(
             'label'=> Mage::helper('sales')->__('Mark Pending'),
             'url'  => $actionNotTakenUrl,
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('palorus/adminhtml_myform/failedtransactionsview/', array('failed_transactions_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        $url = $this->getUrl('*/*/failedtransactions', array('_current'=>true));
        return $url;
    }

}