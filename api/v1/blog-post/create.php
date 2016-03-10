<?php
	
	header('Access-Control-Allow-Origin: *');

	// Require the post library.
	require(getenv("DOCUMENT_ROOT")."/v1/lib/postlib.php");

	// If the server doesn't detect a POST request,
	// early exit.
	if($_SERVER['REQUEST_METHOD'] != "POST") {
 		print("Error: not POST request.");
 		exit();
 	}

 	// Store the post data.
 	$newPost = $_POST["blog-content"];

 	// Check if the new post data is non-null and is actually set.
 	if (isset($newPost)) {
 		$posts->CreatePost($newPost);
 	} else {
 		print("Error: no post data.");
		exit();
 	}
 	
?>