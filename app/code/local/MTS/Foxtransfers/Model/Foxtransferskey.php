<?php

class MTS_Foxtransfers_Model_Foxtransferskey extends Mage_Core_Model_Config_Data
{

	public function save()
	{
		
		if ( isset($_FILES['groups']['tmp_name']['foxtransfers']['fields']['foxtransferskey']) and (file_exists($_FILES['groups']['tmp_name']['foxtransfers']['fields']['foxtransferskey']['value'])) ) {
			
			$key_content = file_get_contents($_FILES['groups']['tmp_name']['foxtransfers']['fields']['foxtransferskey']['value']);
			
			$cfg = new Mage_Core_Model_Config();
			
			$cfg->saveConfig('payment/foxtransfers/foxtransferskeycontent', $key_content);
			
			return parent::save();
			
		}
		
		else
		
		{
		
			if (isset($_POST['groups']['foxtransfers']['fields']['foxtransferskey']['value']['delete']) AND $_POST['groups']['foxtransfers']['fields']['foxtransferskey']['value']['delete'] == 1) 
			{
			
				$cfg = new Mage_Core_Model_Config();
				
				$cfg->saveConfig('payment/foxtransfers/foxtransferskeycontent', null);
				$cfg->saveConfig('payment/foxtransfers/foxtransferskey', null);
				
				return;
			
			}
		
		}
		
	}

}

?>