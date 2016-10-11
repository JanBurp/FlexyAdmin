<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup models
 * Tools voor versiebeheer
 *
 * @author Jan den Besten
 * @internal
 */
class Version extends CI_Model {
  
  private $version   = 'unknown';
  private $revision  = '0';
  private $hash      = '0';
  private $date      = '';
  private $build     = '';
  private $buildFile = 'sys/build.txt';

	public function __construct() {
		parent::__construct();

    // version
    if (file_exists('sys/package.json')) {
      $package=file_get_contents('sys/package.json');
      preg_match("/\"version\"\s?:\s?\"(.*)\"/uim", $package,$matches);
      $this->version = $matches[1];
    }

    // revision (commit count)
    exec('git rev-list --all --count',$output);
    if (!empty($output) and is_numeric($output)) {
      $this->revision = current($output);
      // commit hash
      exec('git rev-parse --verify HEAD 2> /dev/null', $output);
      if (!empty($output)) {
        $this->hash = substr($output[1],0,8);
      }
      // date last commit
      exec("git show", $output);
      if (isset($output[2])) {
        $this->date = date('Y-m-d H:i:s',strtotime((trim(str_replace('Date:','',$output[4])))));
      }
      // build
      $this->build = $this->version.' ['.$this->revision.'] {'.$this->hash.'} '.$this->date;
      write_file($this->buildFile, $this->build);
    }
    else {
      // build without git present, just from file
      $this->build = read_file( $this->buildFile );
      if (preg_match('/^(\d\.\d\.\d*)\s\[(\d*)]\s{([0-9a-zA-Z]*)}\s(.*)/u', $this->build, $matches)) {
        $this->version  = $matches[1];
        $this->revision = $matches[2];
        $this->hash     = $matches[3];
        $this->date     = $matches[4];
      }
    }
    
	}

  public function get_version() {
    return $this->version;
  }

  public function get_revision() {
    return $this->revision;
  }
  
  public function get_hash() {
    return $this->hash;
  }
  
  public function get_build() {
    return $this->build;
  }
  

}

