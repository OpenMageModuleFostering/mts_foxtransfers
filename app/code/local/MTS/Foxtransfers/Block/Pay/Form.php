<?php

class MTS_Foxtransfers_Block_Pay_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
    	$this->setTemplate('foxtransfers/form.phtml');
    	
        parent::_construct();
    }
}

?>