<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/** \ingroup plugins
 * Met deze plugin worden alle language files in site/language/.. omgezet naar de tabel cfg_lang
 *
 * @author Jan den Besten
 */

class Plugin_import_cfg_lang extends Plugin {

  /**
   * @author Jan den Besten
   * @internal
   */
	public function __construct() {
		parent::__construct();
	}

  /**
   * @author Jan den Besten
   * @internal
   */
	public function _admin_api() {
		if ($this->CI->flexy_auth->is_super_admin()) {
      $language_files = scan_map('site/language','php',true);
      $keys=array();
      // collect keys and languages
      foreach ($language_files as $file) {
        $clean_name = str_replace('site/language/','',$file);
        $language = get_prefix($clean_name,'/');
        // load file and get keys and translations
        $text = file_get_contents($file);
        if (preg_match_all("/lang\['(.*)'\]\s(.*;)/uUsx", $text,$matches)) {
          $sub_keys=$matches[1];
          foreach ($sub_keys as $nr => $sub_key) {
            if (!isset($keys[$sub_key])) $keys[$sub_key] = array();
            $keys[$sub_key][$language] = trim($matches[2][$nr],"=;'\" \t\n\r\0\x0B");
          }
        }
      }
      
      // Stop in cfg_lang
      $this->CI->data->table('cfg_lang');
      foreach ($keys as $key => $value) {
        $this->add_message($key);
        $update = $this->CI->data->table('cfg_lang')->where($key,'key')->get_row();
        $this->CI->data->set('key',$key);
        foreach ($value as $lang => $line) {
          $this->CI->data->set('lang_'.$lang,$line);
        }
        if ($update) {
          $this->CI->data->where('key',$key);
          $this->CI->data->update('cfg_lang');
        }
        else {
          $this->CI->data->insert('cfg_lang');
        }
      }
      
		}
		return $this->view('admin/plugins/plugin');
	}
  


}

?>
