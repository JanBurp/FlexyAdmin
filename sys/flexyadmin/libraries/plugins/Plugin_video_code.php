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

  private $fields;
  private $field_types;
  
  public function __construct() {
    parent::__construct();
    $this->fields = $this->config['trigger']['fields'];
    $this->field_types = $this->config['trigger']['field_types'];
    $this->CI->load->helper('video');
  }

	public function _admin_api($args=NULL) {
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;

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
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;

    $this->newData=$this->_get_video_codes($this->newData);
		return $this->newData;
	}


  private function _get_video_codes($item) {
    if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;
    
    foreach ($item as $field => $value) {
      $pre = get_prefix($field);
      if ( in_array($pre,$this->field_types) or in_array($field,$this->fields)) {
        $item[$field] = get_video_code_from_url($item[$field]);
      }
    }

    return $item;
  }

	
}

?>