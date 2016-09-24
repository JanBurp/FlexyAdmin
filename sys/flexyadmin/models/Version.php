<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup models
 * Tools voor versiebeheer
 *
 * @author Jan den Besten
 * @internal
 */
class Version extends CI_Model {
  
  private $version = '';
  private $hash    = '';
  private $date    = '';
  private $build   = '';

	public function __construct() {
		parent::__construct();
    $this->set_version();
    $this->get_version();
    $this->get_hash();
    exec("git show", $output);
    $this->date = date('Y-m-d H:i:s',strtotime((trim(str_replace('Date:','',$output[2])))));
    $this->build = $this->version.' ['.$this->hash.'] '.$this->date;
    $revfile="sys/build.txt";
    write_file('sys/build.txt', $this->build);
	}

  public function set_version() {
    $this->version = 'unkown';
    if (file_exists('sys/package.json')) {
      $package=file_get_contents('sys/package.json');
      preg_match("/\"version\"\s?:\s?\"(.*)\"/uim", $package,$matches);
      $this->version = $matches[1];
    }
  }

  
  public function get_version() {
    if (empty($this->version)) {
      $this->set_version();
    }
    return $this->version;
  }
  
  public function get_hash() {
    if (empty($this->hash)) {
      exec('git rev-parse --verify HEAD 2> /dev/null', $output);
      $this->hash = substr($output[0],0,8);
    }
    return $this->hash;
  }
  
  public function get_build() {
    if (empty($this->build)) {
      $this->build = read_file('sys/build.txt');
    }
    return $this->build;
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

