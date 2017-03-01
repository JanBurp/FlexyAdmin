<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Vervang alle urls van YouTube/Vimeo videos door hun code in de volgende velden:
 * - str_video
 * - str_youtube
 * - str_vimeo
 * 
 * Geef /table
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Plugin_video_code extends Plugin {

  var $fields;
  
  public function __construct() {
    parent::__construct();
    $this->fields = $this->config['trigger']['fields'];
    $this->CI->load->helper('video');
  }

	public function _admin_api($args=NULL) {
		if (isset($args)) {
			if (isset($args[0])) {
				$table=$args[0];
        $items=$this->CI->data->table($table)->get_result();
        foreach ($items as $id  => $item) {
          $items[$id]=$this->_get_video_codes($item);
        }
        $this->add_message("<p>All video urls in <b>$this->table</b> are translated to video codes.</p>");
			}
		}
    return $this->show_messages();
	}



	public function _after_update() {
    $this->newData=$this->_get_video_codes($this->newData);
		return $this->newData;
	}


  private function _get_video_codes($item) {
    foreach ($this->fields as $field) {
      if (isset($item[$field])) {
        $item[$field]=get_video_code_from_url($item[$field]);
      }
    }
    return $item;
  }

	
}

?>