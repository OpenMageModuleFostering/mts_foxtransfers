<?php
class MTS_Foxtransfers_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract  
{ 

	protected $_code  = 'foxtransfers';  

	protected $_formBlockType = 'foxtransfers/pay_form';

	//protected $_canAuthorize=true;

	public function assignData($data)
	{
		$details = array();
		if ($this->getPayableTo()) {
			$details['payable_to'] = $this->getPayableTo();
		}
		
		if ($this->getMailingAddress()) {
			$details['mailing_address'] = $this->getMailingAddress();
		}
		
		if (!empty($details)) {
			$this->getInfoInstance()->setAdditionalData(serialize($details));
		}
		
		return $this;
		
	}

	public function getPayableTo(){
		return $this->getConfigData('payable_to');
	}

	public function getMailingAddress(){
		return $this->getConfigData('mailing_address');
	}

	public function getOrderPlaceRedirectUrl()
	{
		return Mage::getUrl('foxtransfers/pay/redirect');
	}

}