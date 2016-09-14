<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup libraries
 *
 * @author Jan den Besten
 */

class MY_Profiler extends CI_Profiler {

	public function run() {
    $output = parent::run();
    $output = '<style>#codeigniter_profiler * legend {display:block} code {display:block;background-color:#FFF;}</style>'.$output;
		return $output;
	}

  

	
}
