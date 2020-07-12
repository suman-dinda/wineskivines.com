<?php
class EncryptionWrapper{
				
	public static function encrypt($card_number='', $encryption_key='3e196ecca93f16512075e4611d7e1608')
	{				
		$encrypted = SaferCrypto::encrypt($card_number, $encryption_key);
		return $encrypted;
	}
	
	public static function decrypt($encrypted_card='',$encryption_key='3e196ecca93f16512075e4611d7e1608')
	{						
		$decrypted = SaferCrypto::decrypt(trim($encrypted_card), $encryption_key);
		return $decrypted;		
	}
	
} /*end class*/