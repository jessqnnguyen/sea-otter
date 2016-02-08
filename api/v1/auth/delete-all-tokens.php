<?php
	require(getenv("DOCUMENT_ROOT")."/v1/lib/authlib.php");
	
	if($_SERVER['REQUEST_METHOD'] != "POST") {
		print("Error: not POST request.");
		exit();
	}
	
	header("Access-Control-Allow-Origin: http://127.0.0.1:81");
	
	$reply = array();
	
	if(isset($_POST["token"]) && $auth->CheckToken($_POST["token"])) {
		$auth->ClearAllTokens();
		$reply["success"] = true;
	}
	else {
		
		$reply["success"] = false;
	}
	
	print(json_encode($reply));
?>