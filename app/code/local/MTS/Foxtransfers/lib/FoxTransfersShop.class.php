<?php
class FoxTransfersShop{

	private $formHtml;
	private $encoding;
	private $key;
	private $signString;
	
	public function __construct($key, $enc=null){
		$this->formHtml="";
		$this->encoding=$enc;
		$this->key=$key;
		$this->signString=null; 
	}
	
	public function convertArray(&$formArray){
		if (!$this->encoding) return NULL;
		foreach($formArray as $key=>$val){
			$formArray[$key]=iconv($this->encoding,"UTF-8",$val);
		}
		return $formArray;
	}
	
	public function makeSignString($formArray,$convert=TRUE,$sort=TRUE){
		if ($sort) ksort($formArray);
		if ($convert) $this->convertArray($formArray);
		$this->signString="";
		$keys=array("amount","currency","encoding","partner","stamp","status","transaction");
		foreach ($keys as $key){
			if (isset($formArray[$key])){
				$length=mb_strlen($formArray[$key],"UTF-8");
				if ($length>0){
					$this->signString.=sprintf ("%03d",$length);
					$this->signString.=$formArray[$key];
				}
			}
		}
		return $this->signString;
	}
	
	public function validateFormArray($formArray){
		if (!is_array($formArray)) return FALSE;
		if (!array_key_exists("partner",$formArray) 
			or !array_key_exists("amount",$formArray)
			or !array_key_exists("transaction",$formArray)
			or strlen($formArray['partner'])<1 
			or strlen($formArray['amount'])<1 
			or strlen($formArray['transaction'])<1  )
				return FALSE;
		return true;
	}
	
	public function generateFormData($formArray){
		if (!$this->validateFormArray($formArray)) return;
		ksort($formArray);
		if (!is_null($this->encoding)) $this->convertArray($formArray);
		$signStr=$this->makeSignString($formArray,FALSE,FALSE);
		$privKey=openssl_get_privatekey($this->key);
		openssl_sign($this->signString,$signature,$privKey);
		openssl_free_key($privKey);
		$signature=base64_encode($signature);
		$signature = preg_replace("/[\r|\n]/", "", $signature);
		$formArray['signature']=$signature;
		return $formArray;
	}
	
	public function generateForm($formArray){
		$str="";
		$formArray=$this->generateFormData($formArray);
		foreach ($formArray as $key=>$val){
			$str.="<input type=\"hidden\" name=\"".$key."\" value=\"".htmlentities($val)."\"/>\n";
		}
		$this->formHtml=$str;
		return $this->formHtml;
		
	}
	
	public function validateData(&$formArray){
		if (!isset($formArray['signature']) or !isset($formArray['transaction']) ) return false;
		$signature=$formArray['signature'];
		unset($formArray['sign']);
		ksort($formArray);
		$signStr=$this->makeSignString($formArray,false,false);
		$pubKey=openssl_get_publickey($this->key);
		return openssl_verify($signStr,base64_decode($signature),$pubKey);
	}
	
	public function validateDataSuccess(&$formArray){
		if (!$this->validateData($formArray)){
			return false;
		}
		else {
			return $formArray['status'];
		}
	}
	
	
}
?>