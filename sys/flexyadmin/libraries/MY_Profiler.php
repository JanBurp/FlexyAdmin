<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup libraries
 *
 * @author Jan den Besten
 */

class MY_Profiler extends CI_Profiler {

	public function run() {
    $output = parent::run();
    $output = '<style>#codeigniter_profiler fieldset {display:block;width:100%;float:none;} #codeigniter_profiler * {font-size:14px;font-family:courier;} #codeigniter_profiler * legend {display:block} code {display:block;background-color:#FFF;}</style>'.$output;
		return $output;
	}

  

	
}
