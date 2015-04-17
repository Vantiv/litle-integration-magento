<?php
/**
 * Created by PhpStorm.
 * User: joncai
 * Date: 3/26/15
 * Time: 4:15 PM
 */ 
class Litle_LPaypal_Block_Payment_Info extends Mage_Payment_Block_Info {

    /**
     * Get some specific information in format of array($label => $value)
     *
     * @return array
     */
    public function getSpecificInformation()
    {
        $results = array(
            'Transaction ID' => $this->getInfo()->getLastTransId(),
        );
        return $results;
    }
}