<?php
class Litle_CreditCard_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isStateOfOrderEqualTo($order, $inOrderState){
        $payment = $order->getPayment();
        $lastTxnId = $payment->getLastTransId();
        Mage::log("Last txn id test: " . $lastTxnId);
        $lastTxn = $payment->getTransaction($lastTxnId);

        if( $lastTxn != null && $lastTxn->getTxnType() === $inOrderState )
        return true;
        else
        return false;
    }

    // TODO:: Needs to be implemented.
    public function isMOPLitleCC($mop){
        return ($mop === "creditcard");
    }

    // TODO:: Needs to be implemented.
    public function isMOPLitleECheck($mop){
        return ($mop === "lecheck");
    }

    public function isMOPLitlePaypal($mop)
    {
        return ($mop === "lpaypal");
    }

    public function isMOPLitle($payment){
        $mop = $payment->getData('method');
        return ($this->isMOPLitleCC($mop) || $this->isMOPLitleECheck($mop) || $this->isMOPLitlePaypal($mop));
    }

    // This method converts dollars to cents, and takes care of trailing decimals if any.
    public function formatAmount($amountInDecimal, $roundUp) {

        return (Mage::app()->getStore()->roundPrice($amountInDecimal) * 100);
    }

    /**
     * Are we using the sandbox?
     *
     * @return boolean
     */
    public function isSandbox()
    {
        $url = Mage::getStoreConfig('payment/CreditCard/url');
        return (stristr($url, '.testlitle.com/sandbox') !== false);
    }

    public function writeFailedTransactionToDatabase($customerId, $orderId, $message, $xmlDocument, $txnType) {
        $orderNumber = 0;
        $isOrderIdNull = ($orderId === null) ? true : false;
        if($orderId === null) {
            $orderId = 0;
        }
        else {
            $order = Mage::getModel("sales/order")->load($orderId);
            $orderNumber = $order->getData("increment_id");
        }
        if($customerId === null) {
            $customerId = 0;
        }
        $config = Mage::getResourceModel("sales/order")->getReadConnection()->getConfig();
        $host = $config['host'];
        $username = $config['username'];
        $password = $config['password'];
        $dbname = $config['dbname'];

        $con = mysql_connect($host,$username,$password);
        $fullXml = $xmlDocument->saveXML();
        if (!$con)
        {
            Mage::log("Failed to write failed transaction to database.  Transaction details: " . $fullXml, null, "litle_failed_transactions.log");
        }
        else {
            $selectedDb = mysql_select_db($dbname, $con);
            if(!$selectedDb) {
                Mage::log("Can't use selected database " . $dbname, null, "litle.log");
            }
            $fullXml = mysql_real_escape_string($fullXml);
            $litleTxnId = XMLParser::getNode($xmlDocument, 'litleTxnId');
            $sql = "insert into litle_failed_transactions (customer_id, order_id, message, full_xml, litle_txn_id, active, transaction_timestamp, order_num) values (" . $customerId . ", " . $orderId . ", '" . $message . "', '" . $fullXml . "', '" . $litleTxnId . "', true, now()," . $orderNumber . ")";
            Mage::log("Sql to execute is: " . $sql, null, "litle.log");
            $result = mysql_query($sql);
            if(!$result) {
                Mage::log("Insert failed with error message: " . mysql_error(), null, "litle.log");
                Mage::log("Query executed: " . $sql, null, "litle.log");
            }

            if(!$isOrderIdNull) {
                $sql = "select * from sales_payment_transaction where order_id = " . $orderId . " order by created_at asc";
                $result = mysql_query($sql);
                Mage::log("Executed sql: " . $sql, null, "litle.log");
                Mage::log($result, null, "litle.log");
                while($row = mysql_fetch_assoc($result)) {
                    Mage::log($row['transaction_id'], null, "litle.log");
                    $sql = "insert into sales_payment_transaction (parent_id, order_id, payment_id, txn_id, parent_txn_id, txn_type, is_closed, created_at, additional_information) values (".$row['transaction_id'].", ".$orderId.", ".$orderId.", ".$litleTxnId.", ".$row['txn_id'].", '".$txnType."', 0,now(),'".serialize(array('message'=>message))."')";
                }
                Mage::log("Sql to execute is: " . $sql, null, "litle.log");
                $result = mysql_query($sql);
                if(!$result) {
                    Mage::log("Insert failed with error message: " . mysql_error(), null, "litle.log");
                    Mage::log("Query executed: " . $sql, null, "litle.log");
                }

                $sql = "insert into sales_flat_order_status_history (parent_id, is_customer_notified, is_visible_on_front, comment, status, created_at, entity_name) values (".$orderId.", 2, 0,'".$message.". Transaction ID: ".$litleTxnId."','processing',now(),'".$txnType."')";
                Mage::log("Sql to execute is: " . $sql, null, "litle.log");
                $result = mysql_query($sql);
                if(!$result) {
                    Mage::log("Insert failed with error message: " . mysql_error(), null, "litle.log");
                    Mage::log("Query executed: " . $sql, null, "litle.log");
                }    
            }

            mysql_close($con);
        }
    }

    public function parseMerchantIdMap($IdMapString)
    {
        $regex_output = array();
        $validate_regex = "/\(( *(\"[A-Z]+\"|'[A-Z]+') *=> *(\"\d+\"|'\d+') *,?)+ *\)/";
        $parse_regex = "/(?P<cur>(\"[A-Z]+\"|'[A-Z]+')) *=> *(?P<id>(\"\d+\"|'\d+'))/";
        $validate_res = preg_match($validate_regex, $IdMapString, $regex_output);
        if ($validate_res) {
            $num_res = preg_match_all($parse_regex, $IdMapString, $regex_output);
            $merchant_map = array();
            $cur_array = $regex_output["cur"];
            $id_array = $regex_output["id"];
            for ($i = 0; $i < $num_res; $i++) {
                $merchant_map[substr($cur_array[$i], 1, -1)] = substr($id_array[$i], 1, -1);
            }
            return $merchant_map;
        } else {
            return false;
        }
    }
}
