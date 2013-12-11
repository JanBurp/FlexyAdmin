<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Subversion goodies
 *
 * @package default
 * @author Jan den Besten
 * @internal
 * @ignore
 */
class Svn extends CI_Model {
  
  var $revision=FALSE;

	public function __construct() {
		parent::__construct();
	}
  
	public function get_revision() {
    if (!$this->revision) {
  		$rev="";
  		$svnfile="sys/.svn/entries";
  		$revfile="sys/build.txt";
  		if (file_exists($svnfile)) {
  			$svn = read_file($svnfile);
  			$svn=explode("\n",$svn);
  			$matches=array_keys($svn,"jan");
  			$fileKey=$matches[count($matches)-1];
  			$fileKey=$matches[0];
  			$revKey=$fileKey-1;
  			$rev = $svn[$revKey];
  			if (!empty($rev)) write_file($revfile, $rev);
  		}
  		if (empty($rev) and file_exists($revfile)) {
  			$rev = read_file($revfile);
  		}
  		if (empty($rev)) $rev="#";
      $this->revision=$rev;
    }
		return $this->revision;
	}
  

}

