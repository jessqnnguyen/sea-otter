<?php
	header('Access-Control-Allow-Origin: *');

	if($_SERVER['REQUEST_METHOD'] != "POST") {
 		print("Error: not POST request.");
 		exit();
 	}

 	// Store the post data.
 	$newPost = $_POST["blog-content"];

 	// Check if the post data is non-null and is actually set.
 	if(isset($newPost)) {
 		
 		// Get the directory path of the JSON file which stores all the blog post data.
 		$postFileName = getenv("DOCUMENT_ROOT")."/../local/blogpostfile.json";
 		$fileExists = file_exists($postFileName);
 		$postFile = createBlogPostFileHandle($postFileName, $fileExists);
 		$fileSize = filesize($postFileName);
 		
 		$contents;
 		$blogPosts = [];
 		
 		// Check if file size is larger than 0 or not empty.
 		if ($fileSize > 0) {
 			// Read the existing blog post file and store in a string.
 			$contents = fread($postFile, $fileSize);
 			// TODO(Jess): Delete later - for debugging.
 			// print($contents);
 			// Parse the contents JSON string into an array.
 			$blogPosts = json_decode($contents, true) ? : [];
 			print($blogPosts);
 			// Clear the blog post file.
 			ftruncate($postFile, 0);
 			// Update the pointer back to the beginning of the file.
 			fseek($postFile, 0);
 		} 
 		// Push the new blog post to the array of blog posts.
 		array_push($blogPosts, $newPost);
 		// Encode and store back into a JSON string.
 		$postJSONString = json_encode($blogPosts);
 		// Update the post file to the new JSON string.
 		fwrite($postFile, $postJSONString);

 		fclose($postFile);

 		// TODO(Jess): Delete later - for debugging.
 		// print($newPost); 
 	} else { 
 	    print("Error: no post data.");
 	    exit();
 	}

 	/**
 	 * Given a file name and whether the file currently exists or not,
 	 * returns a blog post file handle with the appropriate r/w mode.
 	 * @param fileName - string file name
 	 * @param fileExists - boolean state of whether file exists
 	 */
 	function createBlogPostFileHandle($fileName, $fileExists) {
 		if ($fileExists) {
 			// Open for reading and writing; place the file pointer at the beginning of the file.
 			$fileHandle = fopen($fileName, "r+");
 		} else {
 			// Open for writing only; place the file pointer at the end of the file. 
 			// If the file does not exist, attempt to create it. 
 			$fileHandle = fopen($fileName, "a");
 		}
 		return $fileHandle;
 	}
?>