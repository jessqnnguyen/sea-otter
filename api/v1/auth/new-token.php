<?php
	require(getenv("DOCUMENT_ROOT")."/v1/lib/authlib.php");
	
	if($_SERVER['REQUEST_METHOD'] != "POST") {
		print("Error: not POST request.");
		exit();
	}
	
	header("Access-Control-Allow-Origin: http://127.0.0.1:81");
	
	$inputtedPassword = $_POST["password"];
	
	$reply = array();
	
	if($inputtedPassword == "testpassword") {
		
		$reply["passwordSuccess"] = true;
		$reply["token"] = $auth->GenerateToken();
	}
	else {
		
		$reply["passwordSuccess"] = false;
	}
	
	print (json_encode($reply));
?>