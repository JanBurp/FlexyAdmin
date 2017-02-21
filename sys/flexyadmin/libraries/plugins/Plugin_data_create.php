<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Maakt Data config en Model
 * 
 * Maakt van gegegeven tabel een data model en config bestand.
 * /data_create/tbl_xxx of /data_create/reset/[tbl_xxx]
 * 
 * @author Jan den Besten
 */
 
class Plugin_data_create extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	
  function _admin_api($args=false) {
		if ($this->CI->flexy_auth->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
        $table = array_shift($args);
				if (isset($table)) $goodArgs=true;
				if ($goodArgs) {
          $this->CI->load->model('data/data_create');
          if ($table=='reset') {
            $table = array_shift($args);
            $this->CI->data_create->resetcache($table);
          }
          else {
            $this->CI->data_create->create($table);
          }
          $this->add_content( $this->CI->data_create->output() );
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Which table?</br></br>Give: /tbl_xxx or /reset/[tbl_xxx]</p>');
			}
      return $this->content;
		}
	}
	
}

?>