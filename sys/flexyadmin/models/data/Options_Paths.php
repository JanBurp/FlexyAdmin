<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup data
 * 
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */


Class Options_Paths extends CI_Model {

	public function __construct( $table='' ) {
		parent::__construct();
	}
  
  
  public function get_options( $info=array() ) {
    $options=array();
		$map=$this->config->item('ASSETS');
		$files=read_map($map,'dir');
    $files=array_unset_keys($files,array('css','fonts','img','js','lists','_thumbcache','less-bootstrap','less-default'));
		$options=array_keys($files);
    array_unshift($options,'');
    $options=array_combine($options,$options);
    return $options;
  }
  


}
