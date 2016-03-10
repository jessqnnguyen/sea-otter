<?php
	
	/**
	 * The Post class allows the client to create and delete
	 * blog posts, pushing all changes to a JSON file stored in 
	 * the /local folder in a file called blogpostfile.json.
	 * This is inaccessible to the web, but visible to PHP code.
	 * The class itself is instantiated as $post at the end
	 * of the file, and added to the scope of all files that
	 * import this.
	 */
	class Post {
		
		// Store the location of the blog posts JSON file.
		protected $postFilename;
		protected $localDirectory;

		/**
		 * Constructer - sets the directory path of the file containing all the 
		 * blog post JSON objects. This path is relative to the document root and
		 * is inaccessible to outside users.
		 */
		function __construct() {
			$this->localDirectory = getenv("DOCUMENT_ROOT")."/../local";
			// The directory path of the JSON file which stores all the blog post data.
			$this->postFilename = $this->localDirectory."/blogpostfile.json";
		}

		/**
		 * Creates a new post by pushing it to the array 
		 * of blog posts stored in the local directory.
		 * @param newPost - non-null string containing the new post JSON object.
		 */
		function CreatePost($newPost) {
			$postFileName = $this->postFilename;
	 		$fileExists = file_exists($postFileName);
	 		$postFile = $this->createBlogPostFileHandle($postFileName, $fileExists);
	 		$fileSize = filesize($postFileName);
	 		
	 		// Store some useful variables for blog post parsing.
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
	 			// print($blogPosts);
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
	 		print($postJSONString); 
		}

		/**
		 *
		 * UTILITY FUNCTIONS
		 *
		 */

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
	
	}

	// Inject an instance of the Post class into the scope.
	$post = new Post();	
?>