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
	
	//If the token is valid
	if(isset($_POST["token"]) && $auth->CheckToken($_POST["token"])) {
		//delete all the tokens and report success
		$auth->ClearAllTokens();
		$reply["success"] = true;
	}
	else {
		//if they don't have authorization, don't do it.
		$reply["success"] = false;
	}
	
	//serialize the reply in json so it can be read by the client.
	print(json_encode($reply));
?>