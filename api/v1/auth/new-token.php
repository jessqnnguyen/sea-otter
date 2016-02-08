<?php
	//Require the authenication library
	require(getenv("DOCUMENT_ROOT")."/v1/lib/authlib.php");
	
	//If the client isn't POSTing, don't even bother
	if($_SERVER['REQUEST_METHOD'] != "POST") {
		print("Error: not POST request.");
		exit();
	}
	
	//Allow the cross-domain flags for local testing
	header("Access-Control-Allow-Origin: http://127.0.0.1:81");
	
	//takes in a password string as an input
	$inputtedPassword = $_POST["password"];
	
	//Reply to this request with an array
	$reply = array();
	
	//if the password matches the string 'testpassword',
	//generate them a brand new token and return it to the,
	if($inputtedPassword == "testpassword") {
		
		$reply["passwordSuccess"] = true;
		$reply["token"] = $auth->GenerateToken();
	}
	else {
		
		$reply["passwordSuccess"] = false;
	}
	
	//Allow the cross-domain flags for local testing
	header("Access-Control-Allow-Origin: http://127.0.0.1:81");
?>