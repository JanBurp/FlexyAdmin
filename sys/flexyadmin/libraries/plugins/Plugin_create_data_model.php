<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * @author Jan den Besten
 */
 
class Plugin_create_data_model extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	
  function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $table=$args[0];
				if (isset($table)) $goodArgs=true;
				if ($goodArgs) {
          $this->CI->load->model('data/data_model_create');
          $this->CI->data_model_create->create($table);
          $this->add_content( $this->CI->data_model_create->output() );
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Which table?</br></br>Give: /tbl_xxx</p>');
			}
      return $this->content;
		}
	}
	
}

?>