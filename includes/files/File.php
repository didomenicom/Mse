<?php
/**
 * Mse - PHP development framework for web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2016 Mike Di Domenico
 * @license https://opensource.org/licenses/MIT
 */
defined("Access") or die("Direct Access Not Allowed");

// TODO: Add config file valid support
class File {
	private static $fileUploadErrors = array(
			1 => 'php.ini max file size exceeded',
			2 => 'html form max file size exceeded',
			3 => 'file upload was only partial',
			4 => 'no file was attached');
	
	private static $fileExtensions = array(
			// Extension        Type                                      Valid
			'ai'    =>  		array('application/postscript'			, false),
			'aif'   =>  		array('audio/x-aiff'					, false),
			'aifc'  =>  		array('audio/x-aiff'					, false),
			'aiff'  =>  		array('audio/x-aiff'					, false),
			'avi'   =>  		array('video/x-msvideo'					, false),
			'bin'   =>  		array('application/macbinary'			, false),
			'bmp'   =>  		array('image/bmp'						, false),
			'class' =>  		array('application/octet-stream'		, false),
			'cpt'   =>  		array('application/mac-compactpro'		, false),
			'css'   =>  		array('text/css'						, false),
			'dcr'   =>  		array('application/x-director'			, false),
			'dir'   =>  		array('application/x-director'			, false),
			'dll'   =>  		array('application/octet-stream'		, false),
			'dms'   =>  		array('application/octet-stream'		, false),
			'doc'   =>  		array('application/msword'				, false),
			'dvi'   =>  		array('application/x-dvi'				, false),
			'dxr'   =>  		array('application/x-director'			, false),
			'eml'   =>  		array('message/rfc822'					, false),
			'eps'   =>  		array('application/postscript'			, false),
			'exe'   =>  		array('application/octet-stream'		, false),
			'gif'   =>  		array('image/gif'						, false),
			'gtar'  =>  		array('application/x-gtar'				, false),
			'htm'   =>  		array('text/html'						, false),
			'html'  =>  		array('text/html'						, false),
			'hqx'   =>  		array('application/mac-binhex40'		, false),
			'jpe'   =>  		array('image/jpeg'						, false),
			'jpg'   =>  		array('image/jpeg'						, false),
			'jpeg'  =>  		array('image/jpeg'						, false),
			'js'    =>  		array('application/x-javascript'		, false),
			'lha'   =>  		array('application/octet-stream'		, false),
			'log'   =>  		array('text/plain'						, false),
			'lzh'   =>  		array('application/octet-stream'		, false),
			'mid'   =>  		array('audio/midi'						, false),
			'midi'  =>  		array('audio/midi'						, false),
			'mif'   =>  		array('application/vnd.mif'				, false),
			'mov'   =>  		array('video/quicktime'					, false),
			'movie' =>  		array('video/x-sgi-movie'				, false),
			'mp2'   =>  		array('audio/mpeg'						, false),
			'mp3'   =>  		array('audio/mp3'						, false),
			'mpe'   =>  		array('video/mpeg'						, false),
			'mpeg'  =>  		array('video/mpeg'						, false),
			'mpg'   =>  		array('video/mpeg'						, false),
			'mpga'  =>  		array('audio/mpeg'						, false),
			'oda'   =>  		array('application/oda'					, false),
			'pdf'   =>  		array('application/pdf'					, true),
			'php'   =>  		array('application/x-httpd-php'			, false),
			'php3'  =>  		array('application/x-httpd-php'			, false),
			'php4'  =>  		array('application/x-httpd-php'			, false),
			'phps'  =>  		array('application/x-httpd-php-source'	, false),
			'phtml' =>  		array('application/x-httpd-php'			, false),
			'png'   =>  		array('image/png'						, false),
			'ppt'   =>  		array('application/vnd.ms-powerpoint'	, false),
			'ps'    =>  		array('application/postscript'			, false),
			'psd'   =>  		array('application/octet-stream'		, false),
			'qt'    =>  		array('video/quicktime'					, false),
			'ra'    =>  		array('audio/x-realaudio'				, false),
			'ram'   =>  		array('audio/x-pn-realaudio'			, false),
			'rm'    =>  		array('audio/x-pn-realaudio'			, false),
			'rtx'   =>  		array('text/richtext'					, false),
			'rtf'   =>  		array('text/rtf'						, false),
			'rpm'   =>  		array('audio/x-pn-realaudio-plugin'		, false),
			'rv'    =>  		array('video/vnd.rn-realvideo'			, false),
			'sea'   =>  		array('application/octet-stream'		, false),
			'shtml' =>  		array('text/html'						, false),
			'sit'   =>  		array('application/x-stuffit'			, false),
			'smi'   =>  		array('application/smil'				, false),
			'smil'  =>  		array('application/smil'				, false),
			'so'    =>  		array('application/octet-stream'		, false),
			'swf'   =>  		array('application/x-shockwave-flash'	, false),
			'tar'   =>  		array('application/x-tar'				, false),
			'text'  =>  		array('text/plain'						, false),
			'tif'   =>  		array('image/tiff'						, false),
			'tiff'  =>  		array('image/tiff'						, false),
			'tgz'   =>  		array('application/x-compressed-tar'	, false),
			'txt'   =>  		array('text/plain'						, false),
			'wav'   =>  		array('audio/x-wav'						, false),
			'wbxml' =>  		array('application/vnd.wap.wbxml'		, false),
			'wmlc'  =>  		array('application/vnd.wap.wmlc'		, false),
			'word'  =>  		array('application/msword'				, false),
			'xht'   =>  		array('application/xhtml+xml'			, false),
			'xhtml' =>  		array('application/xhtml+xml'			, false),
			'xl'    =>  		array('application/excel'				, false),
			'xls'   =>  		array('application/vnd.ms-excel'		, false),
			'xml'   =>  		array('text/xml'						, false),
			'xsl'   =>  		array('text/xml'						, false),
			'zip'   =>  		array('application/zip'					, false)
	);
	
	public static function save($path, $contents){
		// Writes contents to disk
	}
	
	/**
	 * Writes the content array (newline deliminated) to the given file
	 * Returns true on success, otherwise false
	 */
	public static function write($path, $contentArray){
		if(strlen($path) > 0 && is_array($contentArray)){
			$fh = fopen($path, 'w');
			
			if($fh != false){
				foreach($contentArray as $item){
					fwrite($fh, $item . "\n");
				}
				
				if(fclose($fh) == true){
					chmod($path, 0777);
					return true;
				}
			}
		}
		
		return false;
	}
	
	public static function append($path, $contentString){
		
	}
	
	public static function getLocation($path){
		// Searches for file in given path
	}
	
	public static function copy($origPath, $newPath){
		// Copies a file
	}
	
	public static function move($origPath, $newPath){
		// Moves a file
	}
	
	public static function open($path){
		// Opens a file handler
		// Check if file exists
		if(File::exists($path)){
			
		}
		
		return NULL;
	}
	
	public static function close($fileHandler){
		// Closes a file handler
	}
	
	public static function cleanupFilename($filename){
		// Makes filename valid
	}
	
	public static function create($path, $contents){
		// Builds file like config.php
	}
	
	/**
	 * Handles multiple uploads
	 * If all are uploaded successfully return true
	 * Otherwise false
	 */
	public static function uploadMultiple($files, $destination){
		if(is_array($files) && strlen($destination) > 0){
			$error = false;
			for($x = 0; $x < count($files['name']); $x++){
				$tmpArray['name'] = $files['name'][$x];
				$tmpArray['type'] = $files['type'][$x];
				$tmpArray['size'] = $files['size'][$x];
				$tmpArray['tmp_name'] = $files['tmp_name'][$x];
				$tmpArray['error'] = $files['error'][$x];
				
				if(File::uploadSingle($tmpArray, $destination) == false){
					$error = true;
				}
			}
			
			if($error == false){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Processes and uploads a single file
	 * Returns true on success
	 * False otherwise
	 */
	public static function uploadSingle($file, $destination, $overwrite = true, $createDestinationIfNotExist = true){
		global $Config;
		
		// Check if the file is valid 
		if(isset($file) && is_array($file) && strlen($destination) > 0){
			// Lets log whats going on
			Log::info("File::uploadSingle Called.\nUpload File: '" . $file['name'] . "'\nSize: '" . $file['size'] . "'\nType: '" . $file['type'] . "'\nError: '" . $file['error'] . "'");

			// Figure out max upload size from the system
			$systemMaxUploadLimit = min(Text::convertFilesizeStringToBytes(ini_get('upload_max_filesize')), 
										Text::convertFilesizeStringToBytes(ini_get('post_max_size')), 
										Text::convertFilesizeStringToBytes(ini_get('memory_limit')));
			
			// Get the max upload size from the config
			$configMaxUploadLimit = $Config->getSystemVar("fileHandling_maxUploadLimit");
			
			// Convert the config max upload limit into bytes
			$configMaxUploadLimit = Text::convertFilesizeStringToBytes($configMaxUploadLimit);
			
			$maxUploadLimit = min($configMaxUploadLimit, $systemMaxUploadLimit);
			
			
			// Handle the destination directory
			if(!File::isDirectory($destination) && !File::isFile($destination)){
				// The directory doesn't exist
				if($createDestinationIfNotExist == true){
					// Create it
					if(File::createDirectory($destination) == false){
						Log::error("File::uploadSingle failed - destination directory creation failed: '" . $destination . "'");
						return false;
					}
					
					Log::info("File::uploadSingle - destination directory created: '" . $destination . "'");
				}
			} elseif(File::isFile($destination)){
				Log::error("File::uploadSingle failed - destination directory is a file: '" . $destination . "'");
				return false;
			}
			
			
			// Handle the file upload
			if(count($file) == 5){
				// Check if there were errors in the upload
				if($file['error'] > 0){
					Log::error("File::uploadSingle failed - upload error: '" . self::$fileUploadErrors[$file['error']] . "'");
					return false;
				}
				
				// Check if the file name is characters only 
				if(Text::verifyStandardCharacters($file['name']) == false){
					Log::error("File::uploadSingle failed - not standard characters: '" . $file['name'] . "'");
					return false;
				}
				
				// Check if the file name is less than 250 chars (Linux limit 255 bytes... http://en.wikipedia.org/wiki/Comparison_of_file_systems#Limits)
				if(strlen($file['name']) > 250){
					Log::error("File::uploadSingle failed - character limit of 250 hit: '" . $file['name'] . "'");
					return false;
				}
				
				// Check if it is a valid file type
				if(!File::validType($file['type'])){
					Log::error("File::uploadSingle failed - invalid type: '" . $file['type'] . "'");
					return false;
				}
				
				// Check if the file exceeded the max file size
				if($file['size'] > $maxUploadLimit){
					Log::error("File::uploadSingle failed - max file size exceeded ('" . $maxUploadLimit . "'): '" . $file['size'] . "'");
					return false;
				}
				
				if(is_uploaded_file($file['tmp_name'])){
					if($overwrite == true || ($overwrite == false && !is_file($path))){
						// This is a new file so upload it
						$fullDestinationPath = Text::trimEndOfString($destination, "/") . "/" . $file['name'];
						if(move_uploaded_file($file['tmp_name'], $fullDestinationPath) == true){
							Log::info("File::uploadSingle success - '" . $fullDestinationPath . "'");
							return true;
						}
					} else {
						Log::error("File::uploadSingle failed - file exists. Cannot overwrite.");
						return false;
					}
				} else {
					// Was not uploaded via POST
					// TODO: Support this option
					Log::warn("File::uploadSingle not submitted via HTTP Post. This functionality is not supported.");
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the filename (and extension) from the full path given
	 */
	public static function getFilename($filePath, $extension = true){
		$parts = explode("/", $filePath);
		
		if(count($parts) > 0){
			if($extension == true){
				return array_pop($parts);
			} else {
				// Rip off the extension
				$fileParts = explode(".", array_pop($parts));
				array_pop($fileParts);
				
				return join(".", $fileParts);
			}
		}
		
		return $filePath;
	}
	
	/**
	 * Returns the permissions on a file
	 * If the file is good then the permissions are returned
	 * Otherwise NULL
	 * PHP.net example
	 */
	public static function getPermissions($filePath, $format = "L"){
		if(strlen($filePath) > 0 && file_exists($filePath) == true){
			if(($filePermissions = fileperms($filePath)) != false){
				switch($format){
					case "B":
						return substr(sprintf('%o', $filePermissions), -4);
						break;
					case "L":
						// Function from http://www.php.net/manual/en/function.fileperms.php Example #2
						if (($filePermissions & 0xC000) == 0xC000) {
							// Socket
							$info = 's';
						} elseif (($filePermissions & 0xA000) == 0xA000) {
							// Symbolic Link
							$info = 'l';
						} elseif (($filePermissions & 0x8000) == 0x8000) {
							// Regular
							$info = '-';
						} elseif (($filePermissions & 0x6000) == 0x6000) {
							// Block special
							$info = 'b';
						} elseif (($filePermissions & 0x4000) == 0x4000) {
							// Directory
							$info = 'd';
						} elseif (($filePermissions & 0x2000) == 0x2000) {
							// Character special
							$info = 'c';
						} elseif (($filePermissions & 0x1000) == 0x1000) {
							// FIFO pipe
							$info = 'p';
						} else {
							// Unknown
							$info = 'u';
						}
						
						// Owner
						$info .= (($filePermissions & 0x0100) ? 'r' : '-');
						$info .= (($filePermissions & 0x0080) ? 'w' : '-');
						$info .= (($filePermissions & 0x0040) ?
								(($filePermissions & 0x0800) ? 's' : 'x' ) :
								(($filePermissions & 0x0800) ? 'S' : '-'));
						
						// Group
						$info .= (($filePermissions & 0x0020) ? 'r' : '-');
						$info .= (($filePermissions & 0x0010) ? 'w' : '-');
						$info .= (($filePermissions & 0x0008) ?
								(($filePermissions & 0x0400) ? 's' : 'x' ) :
								(($filePermissions & 0x0400) ? 'S' : '-'));
						
						// World
						$info .= (($filePermissions & 0x0004) ? 'r' : '-');
						$info .= (($filePermissions & 0x0002) ? 'w' : '-');
						$info .= (($filePermissions & 0x0001) ?
								(($filePermissions & 0x0200) ? 't' : 'x' ) :
								(($filePermissions & 0x0200) ? 'T' : '-'));
						return $info;
						break;
					default:
						return $filePermissions;
						break;
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Gets the file type from the array and returns it. 
	 * If checkExtesion is enabled and the type is not valid it will return NULL
	 * If the type exists it returns the type
	 * Otherwise NULL 
	 */
	public static function getExtension($path, $returnType = false, $checkExtension = false){
		$parts = explode(".", $path);
		
		if(count($parts) > 0){
			$extension = array_pop($parts);
			
			if(array_key_exists($extension, self::$fileExtensions)){
				if($checkExtension == true){
					if(self::$fileExtensions[$extension][1] == true){
						return ($returnType == true ? self::$fileExtensions[$extension][0] : $extension);
					}
				} else {
					return ($returnType == true ? self::$fileExtensions[$extension][0] : $extension);
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Gets the last modified date of a file
	 * Returns the date if success
	 * Otherwise returns NULL
	 */
	public static function getLastModified($filePath, $format = "M/D/Y HIS"){
		if(strlen($filePath) > 0 && file_exists($filePath) == true){
			if(($fileLastModified = filemtime($filePath)) != false){
				switch($format){
					case "M/D/Y":
						return date("m/d/Y", $fileLastModified);
						break;
					case "M/D/Y HIS":
						return date("m/d/Y H:i:s", $fileLastModified);
						break;
					case "Y/M/D HIS":
						return date("Y-m-d H:i:s", $fileLastModified);
						break;
					default:
						return $fileLastModified;
						break;
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Gets the size of a file
	 * Returns the size if success
	 * Otherwise returns NULL
	 */
	public static function getSize($filePath, $type = "K", $sigFig = 2){
		if(strlen($filePath) > 0 && file_exists($filePath) == true){
			if(($fileSize = filesize($filePath)) != false){
				switch($type){
					case "K":
						return number_format(($fileSize / pow(1000, 1)), $sigFig, '.', '');
						break;
					case "M":
						return number_format(($fileSize / pow(1000, 2)), $sigFig, '.', '');
						break;
					case "G":
						return number_format(($fileSize / pow(1000, 3)), $sigFig, '.', '');
						break;
					default:
						return $fileSize;
						break;
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Returns if the path passed in is a directory
	 * True if it is a directory
	 * Otherwise false
	 */
	public static function isDirectory($path){
		if(strlen($path) > 0){
			if(is_dir($path)){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Returns if the path passed in is a file
	 * True if it is a file
	 * Otherwise false
	 */
	public static function isFile($path){
		if(strlen($path) > 0){
			if(is_file($path)){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Deletes a file
	 * If the file exists it is deleted
	 * On success return true
	 * Otherwise false
	 */
	public static function delete($path){
		if(file_exists($path)){
			unset($path);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks if the filename exists in the given path
	 * Returns true if it exists
	 * Otherwise false
	 */
	public static function exists($path, $filename){
		// Checks if file exists
		if(isset($path) && isset($filename)){
			if(file_exists($path . "/" . $filename)){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Checks if the file type passed in is allowed in the system. 
	 * If the file type exists in the table and it is set to true (second index) then return true
	 * Otherwise return false
	 */
	public static function validType($inputType){
		if(strlen($inputType) > 0){
			foreach(self::$fileExtensions as $extensionKey => $extensionArray){
				if($extensionArray[0] === $inputType){
					if($extensionArray[1] == true){
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Checks if the file extension passed in is allowed in the system. 
	 * If the file extion exists in the table and it is set to true (second index) then return true
	 * Otherwise return false
	 */
	public static function validExtension($inputExtension){
		if(strlen($inputExtension) > 0 && array_key_exists($inputExtension, self::$fileExtensions)){
			if(self::$fileExtensions[$inputExtension][1] == true){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Reads the files in a given directory (full path)
	 * If the path passed in is a directory it will return an array with the file names in that directory
	 * If it is not a directory or doesn't exist, it returns NULL
	 */
	public static function directoryContents($path){
		// Puts all of the filenames of a directory in an array
		// Check if path exists as a dir
		if(is_dir($path)){
			$output = array();
			
			// Is a directory read it
			$dir = opendir($path);
			
			// Loop through dir
			while(($file = readdir($dir)) !== false){
				if($file !== "." && $file !== ".."){
					array_push($output, $file);
				}
			}
			
			closedir($dir);
			
			return $output;
		} else {
			Log::error("File: directoryContents -- path is not a directory - path = '" . $path . "'");
		}
		
		return NULL;
	}
	
	/**
	 * Reads the contents of the file passed in (full path)
	 * If the file exists and PHP has read access to it, the contents are read into an array
	 * with the newline character as the delimiter. It will return the array of lines read in
	 * If the file doesn't exist or cannot read it, returns NULL
	 */
	public static function read($fileName){
		if(isset($fileName) && $fileName != ""){
			// Check if file exists
			if(file_exists($fileName)){
				// Open file
				$filePointer = fopen($fileName, "r");
				
				if($filePointer != FALSE){
					// Finally, read it into array
					$outputArray = array();
					while(!feof($filePointer)){
						array_push($outputArray, fgets($filePointer));
					}
					
					fclose($filePointer);
					
					return $outputArray;
				} else {
					Log::error("File: readFile -- unable to read file - name = '" . $fileName . "'");
				}
			} else {
				Log::error("File: readFile -- file doesn't exist - name = '" . $fileName . "'");
			}
		} else {
			Log::info("File: readFile -- filename empty");
		}
		
		return NULL;
	}
}

?>
