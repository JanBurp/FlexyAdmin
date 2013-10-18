<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| MIME TYPES
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

$mimes = array(
				
				'ai'		=>	'application/postscript',
				'aif'		=>	'audio/x-aiff',
				'aifc'	=>	'audio/x-aiff',
				'aiff'	=>	'audio/x-aiff',
				'avi'		=>	'video/x-msvideo',
				'bin'		=>	'application/macbinary',
				'bmp'		=>	array('image/bmp', 'image/x-windows-bmp'),
				'class'	=>	'application/octet-stream',
				'cpt'		=>	'application/mac-compactpro',
				'css'		=>	'text/css',
				'csv'		=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel','application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
				'dcr'		=>	'application/x-director',
				'dir'		=>	'application/x-director',
				'dll'		=>	'application/octet-stream',
				'dms'		=>	'application/octet-stream',
				'doc'		=>	'application/msword',
        'docx'  =>  array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/octet-stream'),
				'dvi'		=>	'application/x-dvi',
				'dxr'		=>	'application/x-director',
				'eml'		=>	'message/rfc822',
				'eps'		=>	'application/postscript',
				'exe'		=>	array('application/octet-stream', 'application/x-msdownload'),
				'flv' 	=>  array('application/octet-stream','video/x-flv'),
				'gif'		=>	'image/gif',
				'gtar'	=>	'application/x-gtar',
				'gz'		=>	'application/x-gzip',
				'hqx'		=>	'application/mac-binhex40',
				'htm'		=>	'text/html',
				'html'	=>	'text/html',
				'jpe'		=>	array('image/jpeg', 'image/pjpeg'),
				'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
				'jpg'		=>	array('image/jpeg', 'image/pjpeg'),
				'js'		=>	'application/x-javascript',
				'json' 	=> array('application/json', 'text/json'),
				'lha'		=>	'application/octet-stream',
				'log'		=>	array('text/plain', 'text/x-log'),
				'lzh'		=>	'application/octet-stream',
				'mid'		=>	'audio/midi',
				'midi'	=>	'audio/midi',
				'mif'		=>	'application/vnd.mif',
				'mov'		=>	'video/quicktime',
				'movie'	=>	'video/x-sgi-movie',
				'mp2'		=>	'audio/mpeg',
				'mp3'		=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
				'mp4'		=>	'video/mp4',
				'mpe'		=>	'video/mpeg',
				'mpeg'	=>	'video/mpeg',
				'mpg'		=>	'video/mpeg',
				'mpga'	=>	'audio/mpeg',
				'oda'		=>	'application/oda',
				'pdf'		=>	array('application/pdf', 'application/x-download', 'application/binary','application/x-pdf','application/unknown'),
				'php'		=>	'application/x-httpd-php',
				'php3'	=>	'application/x-httpd-php',
				'php4'	=>	'application/x-httpd-php',
				'phps'	=>	'application/x-httpd-php-source',
				'phtml'	=>	'application/x-httpd-php',
				'png'		=>	array('image/png',  'image/x-png'),
				'ppt'		=>	array('application/powerpoint', 'application/vnd.ms-powerpoint','application/msword'),
				'pptx'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
				'ps'		=>	'application/postscript',
				'psd'		=>	'application/x-photoshop',
				'qt'		=>	'video/quicktime',
				'ra'		=>	'audio/x-realaudio',
				'ram'		=>	'audio/x-pn-realaudio',
				'rm'		=>	'audio/x-pn-realaudio',
				'rpm'		=>	'audio/x-pn-realaudio-plugin',
				'rtf'		=>	'text/rtf',
				'rtx'		=>	'text/richtext',
				'rv'		=>	'video/vnd.rn-realvideo',
				'sea'		=>	'application/octet-stream',
				'shtml'	=>	'text/html',
				'sit'		=>	'application/x-stuffit',
				'smi'		=>	'application/smil',
				'smil'	=>	'application/smil',
				'so'		=>	'application/octet-stream',
				'sql'		=>  array('text/x-sql','application/octet-stream','text/plain','text/x-lisp'),
				'swf'		=>	'application/x-shockwave-flash',
				'tar'		=>	'application/x-tar',
				'text'	=>	'text/plain',
				'tgz'		=>	array('application/x-tar', 'application/x-gzip-compressed'),
				'tif'		=>	'image/tiff',
				'tiff'	=>	'image/tiff',
				'txt'		=>	array('text/plain','text/x-lisp'),
				'wav'		=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
				'wbxml'	=>	'application/wbxml',
				'wmlc'	=>	'application/wmlc',
				'word'	=>	array('application/msword', 'application/octet-stream'),
				'xht'		=>	'application/xhtml+xml',
				'xhtml'	=>	'application/xhtml+xml',
				'xl'		=>	'application/excel',
				'xls'		=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
				'xlsx'	=>	array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
				'xml'		=>	'text/xml',
				'xsl'		=>	'text/xml',
				'zip'		=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed')
			);



/* End of file mimes.php */
/* Location: ./system/application/config/mimes.php */