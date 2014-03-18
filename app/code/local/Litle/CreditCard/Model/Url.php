<?php

class  Litle_CreditCard_Model_URL
{
	public function toOptionArray()
	{
		return array(
			array(
	                'value' => "https://www.testlitle.com/sandbox/communicator/online",
	                'label' => 'Sandbox'
			),
			array(
			         'value' => "https://postlive.litle.com/vap/communicator/online",
			         'label' => 'Postlive'
			),
            array(
                     'value' => "https://prelive.litle.com/vap/communicator/online",
                     'label' => 'Prelive'
            ),
            array(
                     'value' => "https://transact-postlive.litle.com/vap/communicator/online",
                     'label' => 'Transact Postlive'
            ),
            array(
                     'value' => "https://transact-prelive.litle.com/vap/communicator/online",
                     'label' => 'Transact Prelive'
            ),
			array(
			         'value' => "https://payments.litle.com/vap/communicator/online",
			         'label' => 'Production'
			),
            array(
                     'value' => "https://transact.litle.com/vap/communicator/online",
                     'label' => 'Transact Production'
            )
		);

	}
}
