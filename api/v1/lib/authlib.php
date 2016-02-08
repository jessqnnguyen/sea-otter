<?php
	/*
		
		The Authentication class controls whether the client is authorised to perform certain functions, like
		creating, deletng, and editing blog posts.
		Authorization is controlled by TOKENS, a string of unique digits, which are stored in the client's cookies.
		A TOKEN represents a single action authorized by the user. They are CONSUMED and rendered non usable once that
		action is performed, but the server typically prodvides the user with another TOKEN upon the successful consumption of the cookie.
		As such, this TOKEN CHAIN is slightly less vunerable to attacks that steal user data (but not by much)
		TOKENS also have a inbuilt expiry date, set to five days.
		TOKENS are stored in the tokenfile.json, located in the /local folder. This is inaccesible to the web, but visible to php code.
		Its stored in JSON format, as the file extension suggests.
		
		The class itself is instansiated as $auth at the bottom of this file, and added to the scope of all files that import this.
		
	*/
	class Authentication {
		
		//these describe the locations of the file that contains the token
		protected $tokenFilename;
		protected $localDirectory;
		
		//this constructor sets the location of the tokenfile to a path relative of the document root,
		//specifically below it, which is inaacesible to outside users.
		function __construct() {
			$this->localDirectory = getenv("DOCUMENT_ROOT")."/../local";
			$this->tokenFilename = $this->localDirectory."/tokenfile.json";
		}
		
		//CheckToken takes in an input token, which is a string that represents the user's token,
		//and compares it to all the tokens in the tokenfile.
		//If a match is found, it returns True, but if it fails for any reason, it returns false.
		function CheckToken($inputToken) {
			
			//opens the file specified by the token name, with the mode set to 'readonly'
			//see http://php.net/manual/en/function.fopen.php for more info on modes.
			$tokenFile = fopen($this->tokenFilename, "r");
			
			//the above function returns false if it can't find the file or for some other reason, this is suitable excuse to return false for the function.
			if($tokenFile == false) {
				return false;
			}
			//Checks the length of the file. If it's zero, then don't bother trying to read it and return false.
			$filesize = filesize($this->tokenFilename);
			
			if($filesize == 0)
			{
				return false;
			}
			
			//read the file into a string, and then decode it from json into an associative array.
			$tokenJSONString = fread($tokenFile,$filesize);
			$savedToken = json_decode($tokenJSONString, true);
			
			//loop through all the tokens looking for matches.
			$tokenMatch = false;
			for($i = 0; $i < count ($savedToken); $i++) {
				
				$tokenObject = $savedToken[$i];
				
				//ignore tokens that don't match, or that are expired.
				if($tokenObject["string"] == $inputToken && $tokenObject["date"] > time()) {
					$tokenMatch = true;
					break;
				}
			}
			//close the file pointer
			fclose($tokenFile);
			
			//and return the results of the match, which would be true if the array found a match, and false if it didn't
			return ($tokenMatch);
		}
		
		//Saves a new token to the tokenfile, taking in a single input that represent the token that is to be inserted.
		function SaveToken($inputToken) {
			
			//if the directory containing the token file doesn't exists, create it.
			if(!is_dir($this->localDirectory))
			{
				mkdir($this->localDirectory);
			}
			
			//opens the file specified by the token name, with the mode set to 'read and write+'
			//see http://php.net/manual/en/function.fopen.php for more info on modes.
			$tokenFile = fopen($this->tokenFilename, "a+");
			
			//if the filesize if longer than zero, read the file and decode it's json.
			$filesize = filesize($this->tokenFilename);
			
			$savedToken = array();
			
			if($filesize > 0) {
			
				$tokenJSONString = fread($tokenFile,$filesize);
				
				$savedToken = json_decode($tokenJSONString, true);
			
			}
			
			//Create the array that will contain the individual token data
			$newToken = array();
			
			//set the expiry date to five days from today, and the string to the value of the token to insert.
			$newToken["date"] = time() + (60 * 60 * 24 * 5);
			$newToken["string"] = $inputToken;
			
			//append it to the end of the array savedToken
			$savedToken[] = $newToken;
			
			//re-encode the new token into a json string
			$tokenJSONString = json_encode($savedToken);
			
			//go to the beginning and delete all the old data
			fseek($tokenFile, 0);
			ftruncate($tokenFile, 0);
			//insert the new data, with the new token appended to the end.
			fwrite($tokenFile, $tokenJSONString);
			//and close the file.
			fclose($tokenFile);
		}
		
		//Delete token loops through all the saved token and deletes those that match the parameter, $inputToken
		function DeleteToken($inputToken) {
			
			//opens the file specified by the token name, with the mode set to 'read and write+'
			//see http://php.net/manual/en/function.fopen.php for more info on modes.
			$tokenFile = fopen($this->tokenFilename, "a+");
			
			//If it can't find the token file, exit out
			if($tokenFile == false) {
				return false;
			}
			//Make sure the file is longer than 0
			$filesize = filesize($this->tokenFilename);
			
			if($filesize == 0)
			{
				return false;
			}
			//read and Decode the string from it's JSON
			$tokenJSONString = fread($tokenFile,$filesize);
			$savedToken = json_decode($tokenJSONString, true);
			
			//Loop through all the tokens in the token faile
			for($i = 0; $i < count($savedToken); $i++) {
				
				$tokenObject = $savedToken[$i];
				
				//If the token's string matchs the input token, delete it.
				if($tokenObject["string"] == $inputToken) {
					array_splice($savedToken,$i,1);
					$i--;
				}
			}
			//re-encode the resulting array
			$tokenJSONString = json_encode($savedToken);
			//delete all the old data
			fseek($tokenFile, 0);
			ftruncate($tokenFile, 0);
			//write the new data
			fwrite($tokenFile, $tokenJSONString);
			//and close the file pointer
			fclose($tokenFile);
		}
		
		//This simply deletes the tokenfile, if it exists.
		//Don't worry, the token file is automatically regenereated by the 'savetoken' function
		function ClearAllTokens()
		{
			if(file_exists($this->tokenFilename))
			{
				unlink($this->tokenFilename);
			}
		}
		
		//Creates a new token, saves it to the token file, and then returns it.
		function GenerateToken() {
			
			$token = (string)random_int(PHP_INT_MIN,PHP_INT_MAX);
			
			$this->SaveToken($token);
			
			return $token;
		}
		
		//Takes in a valid token as an input, and deletes it, before finally
		//returning a new valid cookie.
		function ConsumeToken($inputToken) {
			
			if($this->CheckToken($inputToken)) {
				$this->DeleteToken($inputToken);
				return $this->GenerateToken();
			}
			else {
				//unless the token isn't valid in the first place, in which it returns false.
				return false;
			}
		}
	}
	//inject an instance of the authentication class into the scope.
	$auth = new Authentication();
?>