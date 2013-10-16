<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package default
 * @author Jan den Besten
 */
 class Comments extends CI_Model {
   
   public function __construct() {
     parent::__construct();
   }

   /**
    * Geeft alle comments van dit item
    *
    * @return array
    * @author Jan den Besten
    */
   public function get_comments($id=false) {
     if (!$id) $id=$this->settings['id'];
     $this->db->where($this->settings['key_id'],$id);
     $comments=$this->db->get_results($this->settings['table']);
     // make nice date format
     foreach ($comments as $id => $comment) {
        $comments[$id]['niceDate']=strftime('%a %e %b %Y %R',mysql_to_unix($comment[$this->settings['field_date']]));
     }
     return $comments;
   }


   /**
    * Geeft aantal comments van dit item
    *
    * @return array
    * @author Jan den Besten
    */
   public function count_comments($id) {
     $this->db->where($this->settings['key_id'],$id);
     $count=$this->db->num_rows($this->settings['table']);
     return $count;
   }
   
}
