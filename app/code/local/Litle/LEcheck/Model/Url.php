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
		                'value' => "https://prelive.litle.com/vap/communicator/online",
		                'label' => 'Prelive'
				),
				array(
		        		'value' => "https://postlive.litle.com/vap/communicator/online",
		                'label' => 'Postlive'
				),
				array(
				        'value' => "https://payments.litle.com/vap/communicator/online",
				        'label' => 'Production'
				)
		);

	}
}
