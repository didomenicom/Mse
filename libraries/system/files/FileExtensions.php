<?php
/**
 * MseBase - PHP system to develop web applications
 * @author Mike Di Domenico
 * @copyright 2008 - 2013 Mike Di Domenico
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
defined("Access") or die("Direct Access Not Allowed");

class FileExtensions {
	private $fileExtensions = array(
			 // Extension         Type                                Valid
				'ai'    =>  array('application/postscript'			, 0), 
				'aif'   =>  array('audio/x-aiff'					, 0), 
				'aifc'  =>  array('audio/x-aiff'					, 0), 
				'aiff'  =>  array('audio/x-aiff'					, 0), 
				'avi'   =>  array('video/x-msvideo'					, 0), 
				'bin'   =>  array('application/macbinary'			, 0), 
				'bmp'   =>  array('image/bmp'						, 0), 
				'class' =>  array('application/octet-stream'		, 0), 
				'cpt'   =>  array('application/mac-compactpro'		, 0), 
				'css'   =>  array('text/css'						, 0), 
				'dcr'   =>  array('application/x-director'			, 0), 
				'dir'   =>  array('application/x-director'			, 0), 
				'dll'   =>  array('application/octet-stream'		, 0), 
				'dms'   =>  array('application/octet-stream'		, 0), 
				'doc'   =>  array('application/msword'				, 0), 
				'dvi'   =>  array('application/x-dvi'				, 0), 
				'dxr'   =>  array('application/x-director'			, 0), 
				'eml'   =>  array('message/rfc822'					, 0), 
				'eps'   =>  array('application/postscript'			, 0), 
				'exe'   =>  array('application/octet-stream'		, 0), 
				'gif'   =>  array('image/gif'						, 0), 
				'gtar'  =>  array('application/x-gtar'				, 0), 
				'htm'   =>  array('text/html'						, 0), 
				'html'  =>  array('text/html'						, 0), 
				'hqx'   =>  array('application/mac-binhex40'		, 0), 
				'jpe'   =>  array('image/jpeg'						, 0),
				'jpg'   =>  array('image/jpeg'						, 0), 
				'jpeg'  =>  array('image/jpeg'						, 0), 
				'js'    =>  array('application/x-javascript'		, 0), 
				'lha'   =>  array('application/octet-stream'		, 0), 
				'log'   =>  array('text/plain'						, 0), 
				'lzh'   =>  array('application/octet-stream'		, 0), 
				'mid'   =>  array('audio/midi'						, 0), 
				'midi'  =>  array('audio/midi'						, 0), 
				'mif'   =>  array('application/vnd.mif'				, 0), 
				'mov'   =>  array('video/quicktime'					, 0), 
				'movie' =>  array('video/x-sgi-movie'				, 0), 
				'mp2'   =>  array('audio/mpeg'						, 0), 
				'mp3'   =>  array('audio/mpeg'						, 0), 
				'mpe'   =>  array('video/mpeg'						, 0), 
				'mpeg'  =>  array('video/mpeg'						, 0), 
				'mpg'   =>  array('video/mpeg'						, 0),
				'mpga'  =>  array('audio/mpeg'						, 0), 
				'oda'   =>  array('application/oda'					, 0), 
				'pdf'   =>  array('application/pdf'					, 0), 
				'php'   =>  array('application/x-httpd-php'			, 0), 
				'php3'  =>  array('application/x-httpd-php'			, 0), 
				'php4'  =>  array('application/x-httpd-php'			, 0), 
				'phps'  =>  array('application/x-httpd-php-source'	, 0), 
				'phtml' =>  array('application/x-httpd-php'			, 0), 
				'png'   =>  array('image/png'						, 0), 
				'ppt'   =>  array('application/vnd.ms-powerpoint'	, 0), 
				'ps'    =>  array('application/postscript'			, 0), 
				'psd'   =>  array('application/octet-stream'		, 0), 
				'qt'    =>  array('video/quicktime'					, 0), 
				'ra'    =>  array('audio/x-realaudio'				, 0), 
				'ram'   =>  array('audio/x-pn-realaudio'			, 0), 
				'rm'    =>  array('audio/x-pn-realaudio'			, 0), 
				'rtx'   =>  array('text/richtext'					, 0), 
				'rtf'   =>  array('text/rtf'						, 0), 
				'rpm'   =>  array('audio/x-pn-realaudio-plugin'		, 0), 
				'rv'    =>  array('video/vnd.rn-realvideo'			, 0), 
				'sea'   =>  array('application/octet-stream'		, 0), 
				'shtml' =>  array('text/html'						, 0), 
				'sit'   =>  array('application/x-stuffit'			, 0),
				'smi'   =>  array('application/smil'				, 0), 
				'smil'  =>  array('application/smil'				, 0), 
				'so'    =>  array('application/octet-stream'		, 0), 
				'swf'   =>  array('application/x-shockwave-flash'	, 0), 
				'tar'   =>  array('application/x-tar'				, 0), 
				'text'  =>  array('text/plain'						, 0), 
				'tif'   =>  array('image/tiff'						, 0), 
				'tiff'  =>  array('image/tiff'						, 0), 
				'tgz'   =>  array('application/x-tar'				, 0), 
				'txt'   =>  array('text/plain'						, 0), 
				'wav'   =>  array('audio/x-wav'						, 0), 
				'wbxml' =>  array('application/vnd.wap.wbxml'		, 0), 
				'wmlc'  =>  array('application/vnd.wap.wmlc'		, 0), 
				'word'  =>  array('application/msword'				, 0), 
				'xht'   =>  array('application/xhtml+xml'			, 0), 
				'xhtml' =>  array('application/xhtml+xml'			, 0), 
				'xl'    =>  array('application/excel'				, 0), 
				'xls'   =>  array('application/vnd.ms-excel'		, 0), 
				'xml'   =>  array('text/xml'						, 0), 
				'xsl'   =>  array('text/xml'						, 0), 
				'zip'   =>  array('application/zip'					, 0)
			);
	
	public function FileExtensions(){
		// Constructor
	}
	
	public function getExtensions(){
		// Return all extensions
		return $this->fileExtensions;
	}
}
?>