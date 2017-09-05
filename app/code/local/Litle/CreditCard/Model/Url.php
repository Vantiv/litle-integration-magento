<?php

class  Litle_CreditCard_Model_URL
{
	public function toOptionArray()
	{
		return array(
			array(
	                'value' => "https://www.testvantivcnp.com/sandbox/communicator/online",
	                'label' => 'Sandbox'
			),
			array(
			         'value' => "https://payments.vantivpostlive.com/vap/communicator/online",
			         'label' => 'Postlive'
			),
            array(
                     'value' => "https://payments.vantivprelive.com/vap/communicator/online",
                     'label' => 'Prelive'
            ),
            array(
                     'value' => "https://transact.vantivpostlive.com/vap/communicator/online",
                     'label' => 'Transact Postlive'
            ),
            array(
                     'value' => "https://transact.vantivprelive.com/vap/communicator/online",
                     'label' => 'Transact Prelive'
            ),
			array(
			         'value' => "https://payments.vantivcnp.com/vap/communicator/online",
			         'label' => 'Production'
			),
            array(
                     'value' => "https://transact.vantivcnp.com/vap/communicator/online",
                     'label' => 'Transact Production'
            )
		);

	}
}
