<?php
	class Authentication {
		
		protected $tokenFilename;
		
		function __construct() {
			$this->tokenFilename = getenv("DOCUMENT_ROOT")."/../local/tokenfile.json";
		}
		
		function CheckToken($inputToken) {
			
			$tokenFile = @fopen($this->tokenFilename, "r");
			
			if($tokenFile == false) {
				return false;
			}
			
			$filesize = filesize($this->tokenFilename);
			
			if($filesize == 0)
			{
				return false;
			}
			
			$tokenJSONString = fread($tokenFile,$filesize);
			$savedToken = json_decode($tokenJSONString, true);
			
			$tokenMatch = false;
			for($i = 0; $i < count ($savedToken); $i++) {
				
				$tokenObject = $savedToken[$i];
				
				if($tokenObject["string"] != "" && $tokenObject["string"] == $inputToken && $tokenObject["date"] > time()) {
					$tokenMatch = true;
					break;
				}
			}
			
			fclose($tokenFile);
			
			return ($tokenMatch);
		}
		
		function SaveToken($inputToken) {
			
			if(!is_dir($this->localDirectory))
			{
				mkdir($this->localDirectory);
			}
			$tokenFile = fopen($this->tokenFilename, "a+");
			
			$filesize = filesize($this->tokenFilename);
			
			if($filesize > 0) {
			
				$tokenJSONString = fread($tokenFile,$filesize);
				
				$savedToken = json_decode($tokenJSONString, true);
			
			}
			
			$newToken = array();
			
			$newToken["date"] = time() + (60 * 60 * 24 * 5);
			$newToken["string"] = $inputToken;
			
			$savedToken[] = $newToken;
			
			$tokenJSONString = json_encode($savedToken);
			
			fseek($tokenFile, 0);
			ftruncate($tokenFile, 0);
			
			fwrite($tokenFile, $tokenJSONString);
			
			fclose($tokenFile);
		}
		
		function DeleteToken($inputToken) {
			
			$tokenFile = fopen($this->tokenFilename, "a+");
			
			if($tokenFile == false) {
				return false;
			}
			
			$filesize = filesize($this->tokenFilename);
			
			if($filesize == 0)
			{
				return false;
			}
			
			$tokenJSONString = fread($tokenFile,$filesize);
			$savedToken = json_decode($tokenJSONString, true);
			
			for($i = 0; $i < count($savedToken); $i++) {
				
				$tokenObject = $savedToken[$i];
				
				if($tokenObject["string"] === $inputToken) {
					array_splice($savedToken,$i,1);
					$i--;
				}
			}
			
			$tokenJSONString = json_encode($savedToken);
			
			fseek($tokenFile, 0);
			ftruncate($tokenFile, 0);
			
			fwrite($tokenFile, $tokenJSONString);
			
			fclose($tokenFile);
		}
		
		function ClearAllTokens()
		{
			if(file_exists($this->tokenFilename))
			{
				unlink($this->tokenFilename);
			}
		}
		
		function GenerateToken() {
			
			$token = (string)random_int(PHP_INT_MIN,PHP_INT_MAX);
			
			$this->SaveToken($token);
			
			return $token;
		}
		
		function ConsumeToken($inputToken) {
			
			if($this->CheckToken($inputToken)) {
				$this->DeleteToken($inputToken);
				return $this->GenerateToken();
			}
			else {
				return false;
			}
		}
	}
	
	$auth = new Authentication();
?>