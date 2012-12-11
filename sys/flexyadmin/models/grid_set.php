<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles the state a grid is in: order,search and page
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */


class Grid_set extends CI_Model {

  var $api='API_view_grid';

  public function set_api($api='API_view_grid') {
    $this->api=$api;
  }

	public function save($set=array()) {
		$default=array('table'=>'','offset'=>'','order'=>'','search'=>'');
		$set=array_merge($default,$set);
    $set['search']=str_replace('~','%',$set['search']);
		$this->session->set_userdata('grid_set',$set);
	}
	
	public function open() {
		$set=$this->session->userdata('grid_set');
		return $set;
	}
	
	public function open_uri($table='') {
		$set=$this->open();
		if (empty($table)) $table=$set['table'];
    // strace_($set);
		$uri=api_uri($this->api,$table);
		unset($set['table']);
		foreach ($set as $key => $value) {
			if (!empty($value)) $uri.="/$key/$value";
		}
    // strace_($uri);
		return $uri;
	}
	
	public function reset() {
		$this->session->unset_userdata('grid_set');
	}


}