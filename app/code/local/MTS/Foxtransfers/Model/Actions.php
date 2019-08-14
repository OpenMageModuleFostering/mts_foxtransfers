<?php

class MTS_Foxtransfers_Model_Actions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'https://www.foxtransfers.lt/transaction/choosebank', 'label'=>Mage::helper('foxtransfers')->__('Real mode')),
            array('value'=>'https://www.foxtransfers.lt/transaction/test/report-payment-success', 'label'=>Mage::helper('foxtransfers')->__('Test mode: report status payment successful')),
			array('value'=>'https://www.foxtransfers.lt/transaction/test/report-payment-canceled', 'label'=>Mage::helper('foxtransfers')->__('Test mode: report status payment canceled')),
			array('value'=>'https://www.foxtransfers.lt/transaction/test/customer-return-payment-success', 'label'=>Mage::helper('foxtransfers')->__('Test mode: customer return payment successful')),
			array('value'=>'https://www.foxtransfers.lt/transaction/test/customer-return-payment-canceled', 'label'=>Mage::helper('foxtransfers')->__('Test mode: customer return payment canceled')),
			array('value'=>'https://www.foxtransfers.lt/transaction/test/sandbox', 'label'=>Mage::helper('foxtransfers')->__('Test mode: sandbox without registration and with common keys')),
        );
    }

}

?>