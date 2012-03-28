<?php

class  Litle_LEcheck_Model_URL
{
	public function toOptionArray()
	{
		return array(

				array(
		                'value' => "https://www.testlitle.com/sandbox/communicator/online",
		                'label' => 'Sandbox'
				),
				array(
		                'value' => "https://cert.litle.com/vap/communicator/online",
		                'label' => 'Cert'
				),
				array(
		        		'value' => "https://precert.litle.com/vap/communicator/online",
		                'label' => 'PreCert'
				),
				array(
				        'value' => "https://payments.litle.com/vap/communicator/online",
				        'label' => 'Production'
				)
		);

	}
}
