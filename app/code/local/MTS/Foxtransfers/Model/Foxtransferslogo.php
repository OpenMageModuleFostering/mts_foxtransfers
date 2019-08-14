<?php

class MTS_Foxtransfers_Model_Foxtransferslogo extends Mage_Core_Model_Config_Data
{

	public function save()
	{

		if ( isset($_FILES['groups']['tmp_name']['foxtransfers']['fields']['logo']) and (file_exists($_FILES['groups']['tmp_name']['foxtransfers']['fields']['logo']['value'])) ) {
			
			$path  = Mage::getBaseDir('media') . DS . 'foxtransfers/';
			
			if(!is_dir($path)){
				mkdir($path, 0777, true);
			}			
			
			$file_path = $path . $_FILES['groups']['name']['foxtransfers']['fields']['logo']['value'];
			
			move_uploaded_file($_FILES['groups']['tmp_name']['foxtransfers']['fields']['logo']['value'], $file_path);
			
			$cfg = new Mage_Core_Model_Config();
			$cfg->saveConfig('payment/foxtransfers/logo', 'foxtransfers/' . $_FILES['groups']['name']['foxtransfers']['fields']['logo']['value']);
			
			return;
		
		}
		
		else 
		
		{
		
			if (isset($_POST['groups']['foxtransfers']['fields']['logo']['value']['delete']) AND $_POST['groups']['foxtransfers']['fields']['logo']['value']['delete'] == 1) 
			{
			
				$path  = Mage::getBaseDir('media') . DS;
			
				$cfg = new Mage_Core_Model_Config();
				$filename = Mage::getStoreConfig('payment/foxtransfers/logo');

				unlink($path . $filename);
				
				$cfg->saveConfig('payment/foxtransfers/logo', null);
				
				return;
			
			}
		
		}
		
	}

}

?>