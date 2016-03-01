<?php
	header('Access-Control-Allow-Origin: *');

	if($_SERVER['REQUEST_METHOD'] != "POST") {
 		print("Error: not POST request.");
 		exit();
 	}

 	$newPost = $_POST["blog-content"];

 	if(isset($newPost)) {
 		
 		$postFileName = getenv("DOCUMENT_ROOT")."/../local/blogpostfile.json";
 		
 		if (!file_exists($postFileName)) {

 		} else {
 			// Open for reading and writing; place the file pointer at the beginning 
	 		// of the file and truncate the file to zero length. If the 
	 		// file does not exist, attempt to create it.
	 		$postFile = fopen($postFileName, "r+");
 		}

 		$postFile = fopen($postFileName, "r+");
 		
 		fwrite($postFile, $newPost);
 		
 		fclose($postFile);

 		print($newPost); 
 	} else {
 	  
 	  print("Error: no post data.");
 	  exit();

 	}

 	function createFileHandle($fileName) {
 		if (file_exists($fileName)) {
 			$fileHandle = fopen($postFileName, "r+");
 		} else {
 			$fileHandle = fopen($postFileName, "a");
 		}
 	}
?>