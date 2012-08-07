<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forum model
 *
 * Beta!
 * Onderdeel van de forum module
 *
 * @package default
 * @author Jan den Besten
 */
 class Forum_model extends CI_Model {

  var $last_time;
  var $limit=10;
  var $offset=0;

  public function set_last_time($last_time) {
    $this->last_time=standard_date('DATE_W3C',$last_time);
  }
  
  public function set_limit($limit,$offset) {
    $this->limit=$limit;
    $this->offset=$offset;
  }

  public function get_index() {
    $index=$this->get_categories();
    foreach ($index as $id => $categorie) {
      $index[$id]['threads']=$this->get_threads($id);
    }
    return $index;
  }

  public function get_categories() {
    return $this->db->get_result('tbl_forum_categories');
  }

	public function get_categorie_uri($categorie_id) {
		return $this->db->get_field_where('tbl_forum_categories','uri','id',$categorie_id);
	}
  
  
  public function get_threads($id_categorie=false) {
    if (!empty($id_categorie)) {
      $this->db->where('id_forum_categories',$id_categorie);
    }
    $threads=$this->db->get_result('tbl_forum_threads');
    foreach ($threads as $id => $thread) {
      $threads[$id]['messages_count']=$this->count_messages($id);
      $threads[$id]['new_messages_count']=$this->count_new_messages($id);
    }
    return $threads;
  }

  public function get_thread_by_uri($uri_thread) {
    $this->db->where('uri',$uri_thread);
    $thread=$this->db->get_row('tbl_forum_threads');
    $messages=$this->get_messages($thread['id'],0);
    return array('thread'=>$thread,'messages'=>$messages,'count_messages'=>count($messages),'total_messages'=>$this->count_messages($thread['id']));
  }
  
	public function get_thread_name($thread_id) {
		return $this->db->get_field_where('tbl_forum_threads','str_title','id',$thread_id);
	}
	
	public function get_thread_uri($thread_id) {
		$categorie_id=$this->db->get_field_where('tbl_forum_threads','id_forum_categories','id',$thread_id);
		$categorie_uri=$this->get_categorie_uri($categorie_id);
		return $categorie_uri.'/'.$this->db->get_field_where('tbl_forum_threads','uri','id',$thread_id);
	}
	

  public function get_messages($id_thread=false) {
    $this->db->order_by('tme_date','ASC');
    if (!empty($id_thread)) {$this->db->where('id_forum_thread',$id_thread);}
    $this->db->add_foreigns(array('cfg_users'=>array('id','str_username','email_email')));
    return $this->db->get_result('tbl_forum_messages',$this->limit,$this->offset);
  }

  public function get_recent_messages($id_thread=false) {
    $this->db->order_by('tme_date','ASC');
    if (!empty($id_thread)) $this->db->where('id_forum_thread',$id_thread);
    if (isset($this->last_time)) $this->db->where('tme_date >=',$this->last_time);
    $this->db->add_foreigns(array('tbl_forum_threads'=>array('uri','id_forum_categories','str_title')));
    $this->db->group_by('id_forum_thread');
    $messages=$this->db->get_result('tbl_forum_messages',$this->limit,$this->offset);
    $categories=array();
    foreach ($messages as $id => $message) {
      $categories_id=$message['tbl_forum_threads__id_forum_categories'];
      if (!isset($categories[$categories_id])) {
        $this->db->select('id,uri,str_title');
        $this->db->where('id',$categories_id);
        $categorie=$this->db->get_row('tbl_forum_categories');
        foreach ($categorie as $field => $value) {
          $categories[$categories_id]['tbl_forum_categories__'.$field]=$value;
        }
      }
      $messages[$id]=array_merge($message,$categories[$categories_id]);
      $messages[$id]['full_uri']=$messages[$id]['tbl_forum_categories__uri'].'/'.$messages[$id]['tbl_forum_threads__uri'];
      $messages[$id]['full_title']=$messages[$id]['tbl_forum_categories__str_title'].' / '.$messages[$id]['tbl_forum_threads__str_title'];
    }
    return $messages;
  }

  public function count_messages($id_thread=false) {
    if (!empty($id_thread)) {
      $this->db->where('id_forum_thread',$id_thread);
    }
    return $this->db->count_all_results('tbl_forum_messages');
  }
  
  public function count_new_messages($id_thread=false) {
    if (!empty($id_thread)) $this->db->where('id_forum_thread',$id_thread);
    if (isset($this->last_time)) $this->db->where('tme_date >=',$this->last_time);
    return $this->db->count_all_results('tbl_forum_messages');
  }

	public function get_email_adresses_for_thread($id_thread,$to_admin=true,$to_users_in_thread=false) {
		// collect all adresses
		$user_ids=array();
    if ($to_admin) {
  		$user_ids[]=$this->db->get_field_where('tbl_forum_threads','id_user','id',$id_thread);
    }
    if ($to_users_in_thread) {
  		$messages=$this->get_messages($id_thread,0);
  		foreach ($messages as $id => $message) {
  			$user_ids[]=$message['id_user'];
  		}
    }
		$user_ids=array_unique($user_ids);
		$this->db->select('id,str_username,email_email');
		$this->db->where_in('id',$user_ids);
		$adresses=$this->db->get_result('cfg_users');
		foreach ($adresses as $id => $adres) {
			$adresses[$id]=$adres['str_username'].' <'.$adres['email_email'].'>';
		}
    $adresses=implode(',',$adresses);
		return $adresses;
	}


}
