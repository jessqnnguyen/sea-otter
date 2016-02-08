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
	
	//Reply to this request with an array
	$reply = array();
	
	//if the token is valid, consume it and give the client back their new token.
	if(isset($_POST["token"])) {
		
		$reply["token"] = $auth->ConsumeToken($_POST["token"]);
		$reply["success"] = !!$reply["token"];
	}
	else {
		
		$reply["success"] = false;
	}
	
	//Allow the cross-domain flags for local testing
	header("Access-Control-Allow-Origin: http://127.0.0.1:81");
?>