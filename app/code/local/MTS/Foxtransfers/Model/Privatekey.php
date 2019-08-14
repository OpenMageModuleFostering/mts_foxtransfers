<?php

class MTS_Foxtransfers_Model_Privatekey extends Mage_Core_Model_Config_Data
{

	public function save()
	{
		
		if ( isset($_FILES['groups']['tmp_name']['foxtransfers']['fields']['privatekey']) and (file_exists($_FILES['groups']['tmp_name']['foxtransfers']['fields']['privatekey']['value'])) ) {
			
			$key_content = file_get_contents($_FILES['groups']['tmp_name']['foxtransfers']['fields']['privatekey']['value']);
			
			$cfg = new Mage_Core_Model_Config();
			
			$cfg->saveConfig('payment/foxtransfers/privatekeycontent', $key_content);
			
			return parent::save();
			
		}
		
		else
		
		{
		
			if (isset($_POST['groups']['foxtransfers']['fields']['privatekey']['value']['delete']) AND $_POST['groups']['foxtransfers']['fields']['privatekey']['value']['delete'] == 1) 
			{
			
				$cfg = new Mage_Core_Model_Config();
				
				$cfg->saveConfig('payment/foxtransfers/privatekeycontent', null);
				$cfg->saveConfig('payment/foxtransfers/privatekey', null);
				
				return;
			
			}
		
		}
		
	}

}

?>