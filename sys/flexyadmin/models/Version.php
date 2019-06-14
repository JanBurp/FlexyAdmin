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
  private $buildFile = 'build.txt';
  private $latest_remote = '';

	public function __construct() {
		parent::__construct();

    if (SAFE_INSTALL) {
      $sys = '../sys/';
    }
    else {
      $sys = 'sys/';
    }
    $this->buildFile = $sys.$this->buildFile;

    // version
    if (file_exists($sys.'package.json')) {
      $package=file_get_contents($sys.'package.json');
      preg_match("/\"version\"\s?:\s?\"(.*)\"/uim", $package,$matches);
      $this->version = $matches[1];
    }

    // revision (commit count)
    $output = '';
    if (IS_LOCALHOST) exec('git rev-list --all --count',$output);
    if (!empty($output)) {
      $this->revision = current($output);
      if ($this->revision<3000) $this->revision = '?';
      // commit hash
      exec('git rev-parse --verify HEAD 2> /dev/null', $output);
      if (!empty($output)) {
        $this->hash = substr($output[1],0,8);
      }
      // date last commit
      exec("git log -1", $output);
      if ( $key=array_preg_search('^Date:\s',$output) ) {
        $key=current($key);
        $this->date = date('Y-m-d H:i:s',strtotime((trim(str_replace('Date:','',$output[$key])))));
      }
      // build
      $this->build = $this->version.' {'.$this->hash.'} ';
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

  public function get_mix_version($file,$backend=false) {
    if (substr($file,0,1)!=='/') $file = '/'.$file;
    $path = $this->config->item('PUBLICASSETS');
    if ($backend) $path = $this->config->item('SYS').'flexyadmin/assets/dist/';
    $manifest = read_file($path.'mix-manifest.json');
    $manifest = json_decode($manifest);
    $version = trim($manifest->$file,'/');
    if ($backend) {
      $version = $this->config->item('ADMINASSETS').$version;
    }
    else {
      $version = $this->config->item('PUBLICASSETS').$version;
    }
    return $version;
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

  public function get_latest_remote() {
    $tag = '';
    exec("git ls-remote --tags https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git", $output);
    if ($output) {
      $tags = array();
      foreach ($output as $key => $line) {
        $line_tag = get_suffix($line,'/');
        if (strlen($line_tag)===5) $tags[] = $line_tag;
      }
      rsort($tags);
      $tag = current($tags);
    }
    return $tag;
  }

  public function get_changelog($version='') {
    if (empty($version)) $version=$this->get_version();
    $changelog = read_file(APPPATH.'../../changelog.txt');
    if (preg_match_all('/(?m)^\=(\d.*)\s*([^\=]*)/ui', $changelog,$matches)) {
      $changelog = array();
      foreach ($matches[1] as $key => $version) {
        $changelog[]=array(
          'version' => $version,
          'log'     => $matches[2][$key],
        );
      }
      return $changelog;
    }
    return FALSE;
  }

}

