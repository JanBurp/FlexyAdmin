<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup models
 * Handige tools voor versiebeheer
 *
 * @author Jan den Besten
 * @internal
 */
class Svn extends CI_Model {
  
  var $revision=FALSE;

	public function __construct() {
		parent::__construct();
	}
  
  public function get_version() {
    $txt=file_get_contents('sys/version.txt');
    return trim($txt);
  }
  
  public function get_revision_of_file($file) {
    $txt=file_get_contents($file);
    if (preg_match('/\$Revision:\s(\d*)\s\$/u', $txt, $matches)) {
      return (int) el(1,$matches,false);
    };
    return false;
  }
  
	public function get_revision() {
    if (!$this->revision) {
  		$rev="";
  		$svnfile="sys/.svn/entries";
  		$revfile="sys/build.txt";
  		if (file_exists($svnfile)) {
  			$svn = file_get_contents($svnfile);
  			$svn=explode("\n",$svn);
  			$matches=array_keys($svn,"jan");
  			$fileKey=$matches[count($matches)-1];
  			$fileKey=$matches[0];
  			$revKey=$fileKey-1;
  			$rev = $svn[$revKey];
  			if (!empty($rev)) write_file($revfile, $rev);
  		}
  		if (empty($rev) and file_exists($revfile)) {
  			$rev = file_get_contents($revfile);
  		}
  		if (empty($rev)) $rev="#";
      $this->revision=$rev;
    }
		return $this->revision;
	}
  
  public function get_revision_of($revfile) {
    $path=remove_suffix($revfile,'/');
    $file=get_suffix($revfile,'/');
    $svnfile=$path.'/.svn/entries';
		$rev='';
		if (file_exists($svnfile)) {
			$svn = file_get_contents($svnfile);
			$svn=explode("\n",$svn);
      if ($line=array_search(strtolower($file),$svn)) {
        $rev_line=$line+9;
        if (isset($svn[$rev_line])) $rev=$svn[$rev_line];
      }
		}
    return $rev;
  }
  
  public function get_last_update_file() {
		$updates=read_map('sys/flexyadmin/models/updates','php',FALSE,FALSE);
		$updates=array_keys($updates);
		$updates=filter_by($updates,'update_');
    sort($updates);
    $last=array_pop($updates);
    $last=get_suffix($last,'/');
    $rev=(int) substr($last,7,4);
    return $rev;
  }
  

}

