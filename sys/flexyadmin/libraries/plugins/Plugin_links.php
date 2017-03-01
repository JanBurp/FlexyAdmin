<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Zorgt ervoor dat links in `tbl_links` goed foruleerd zijn.
 * 
 * Zorgt ervoor dat alle links goed geformuleerd zijn:
 * - Altijd met een protocol ervoor (http://, https:// of mailto:)
 * - Altijd enkele / tussen parts
 * - Aan het einde nooit een /
 *
 * @author Jan den Besten
 * @internal
 */

class Plugin_links extends Plugin {

	public function __construct() {
		parent::__construct();
		$this->CI->load->model('search_replace');
		$this->CI->load->library('form_validation');
	}
  
  public function _admin_api($args,$help) {
		if (!$this->CI->flexy_auth->is_super_admin()) return;
    $this->add_message($help['long'].'<hr>');
    // Prep urls
    $links = $this->CI->data->table('tbl_links')->select('str_title,url_url')->get_result();
    $this->add_message('<ul>');
    foreach ($links as $id => $link) {
      $this->add_message('<li>');
      $old_link = $link['url_url'];
      $new_link = $this->CI->form_validation->prep_url_mail($old_link);
      $show_link = '<a class="text-primary" href="'.$new_link.'" target="_blank">'.$new_link.'</a>';
      if ($old_link!==$new_link) {
        $this->add_message('<span class="text-danger">'.$old_link.' <span class="text-warning">=></span> '.$show_link);
        $this->CI->data->table('tbl_links')
                      ->set('url_url',$new_link)
                      ->where('url_url',$old_link)
                      ->update();
      }
      else {
        $this->add_message('<span class="text-primary">'.$show_link.'</span>');
      }
      $this->add_message('</li>');
    }
    $this->add_message('</ul>');
    return $this->show_messages();
  }
	
	public function _after_update() {
		$this->_update_links_in_text();
		return $this->newData;
	}
	
	public function _after_delete() {
		$linkTable='tbl_links';
		$menuTable=get_menu_table();
		if ($this->table==$linkTable or $this->table==$menuTable) {
			$this->newData=array();
			$this->_update_links_in_text();
		}
		return TRUE;
	}
  
	
	public function _update_links_in_text() {
		// what is changed?
    $changedFields=array_diff_multi($this->oldData,$this->newData);
		foreach ($changedFields as $field => $value) {
			$pre=get_prefix($field);
			if (!in_array($field,$this->trigger['fields']) and !in_array($pre,$this->trigger['field_types'])) unset($changedFields[$field]);
		}
    
		// loop through all changed fields, and replace all links with new
		foreach ($changedFields as $field => $value) {
			$oldUrl=$this->oldData[$field];
			if (!empty($oldUrl)) {
				$newUrl='';
				if (isset($this->newData[$field])) {
					$newUrl=$this->newData[$field];
				}
				if ($field=='uri' and isset($this->oldData['self_parent'])) {
					$oldUrl=$this->_getFullParentUri($this->oldData);
					$newUrl=remove_suffix($oldUrl,'/').'/'.$newUrl;
				}
				$this->CI->search_replace->links($oldUrl,$newUrl);
			}
		}
	}

	private function _getFullParentUri($data) {
    $this->CI->data->table( $this->table );
    $this->CI->data->tree('uri');
    $this->CI->data->where($data['id']);
		$full = $this->CI->data->get_row();
    return $full['uri'];
	}
	

}

?>