<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plugin_joomla extends Plugin {
   
   public function __construct() {
     parent::__construct();
     $this->CI->load->dbforge();
   }

	public function _admin_api($args=NULL) {
    // $this->merge_text('tbl_blog');
    $this->replace_images('tbl_blog');
    $this->replace_downloads('tbl_blog');
    return $this->view();
	}

  
  private function merge_text($table) {
    $items=$this->CI->db->get_results($table);
    foreach ($items as $id => $item) {
      $intro=$item['txt_introtext'];
      $full=$item['txt_fulltext'];
      $txt=$intro.$full;
      $this->CI->db->set('txt_text',$txt);
      $this->CI->db->where('id',$id);
      $this->CI->db->update($table);
    }
  }
  
  private function replace_images($table) {
    $items=$this->CI->db->get_result($table);
    foreach ($items as $id => $item) {
      $txt=$item['txt_text'];
      if (preg_match_all("/<img(.*)src=\"(.*)?\"/uiUsm", $txt,$matches)) {
        $images=$matches[2];
        foreach ($images as $key => $img) {
          $file=get_suffix($img,'/');
          $new_file=clean_file_name($file);
          $new_img='site/assets/pictures/'.$new_file;
          $txt=str_replace($img,$new_img,$txt);
          trace_('Image ['.$id.'] '.$new_img );
        }
      }
      $this->CI->db->set('txt_text',$txt)->where('id',$id)->update($table);
    }
  }

  private function replace_downloads($table) {
    $items=$this->CI->db->get_result($table);
    foreach ($items as $id => $item) {
      $txt=$item['txt_text'];
      if (preg_match_all("/<a(.*?)href=\"(.*)?\"/uiUsm", $txt,$matches)) {
        $links=$matches[2];
        foreach ($links as $key => $link) {
          if (substr($link,0,7)=='mailto:') break;
          if (substr($link,0,4)=='http') break;
          if (substr($link,0,9)=='index.php') {
            trace_('Internal link ['.$id.'] '.$link );
          }
          elseif (has_string('/',$link)) {
            $file=get_suffix($link,'/');
            $new_file=clean_file_name($file);
            $new_link='site/assets/downloads/'.$new_file;
            $txt=str_replace($link,$new_link,$txt);
            trace_('Download ['.$id.'] '.$link );
          }
        }
      }
      $this->CI->db->set('txt_text',$txt)->where('id',$id)->update($table);
    }
  }

  
  

}

?>