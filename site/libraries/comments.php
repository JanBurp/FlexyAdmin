<?

/*

Add comments to a page
Uses views/comments.php
And needs database: db/add_comments.sql
Change tbl_comments as you wish.
If you use res_menu_result instead of tbl_menu for a menu:
- change tbl_comments.id_menu to tbl_comments.id_menu_result and
- In this file: change all 'id_menu' in 'id_menu_result'
- And $item['id] in $item['int_id] (on line 34,68)

SUCCES!

*/

class Comments extends Module {

	function __construct() {
		parent::__construct();
		$this->CI->load->model('form');
		$this->CI->load->library('spam');
	}

	function module($item) {
		$table='tbl_comments';
		$formHtml='';
		$errorHtml='';

		// set form klaar
		$formData=array();
		$formData=$this->_setform_fields($table);
		$formData['spambody']=array('label'=>'','type'=>'textarea','value'=>'','attr'=>array('style'=>'display:none;'));  // voor de spamcheck
		$formData['id_menu']=array('type'=>'hidden','value'=>$item['id']);
		unset($formData['int_spamscore']);

		$form=new form($this->CI->uri->get());
		$form->set_data($formData,"Comments");
		$form->set_buttons(array('submit'=>array("submit"=>"submit","value"=>'Plaats comment')));
	
	
		// Validate form, if succes, add comment
		if ($form->validation()) {
			$data=$form->get_data();
			$data['datetime_datum']=date(DATE_ISO8601);
			// Check for spam
			$spam=FALSE;
			if (!$spam and $this->CI->_check_if_robot($data)) 				$spam=TRUE;	// Check if a robot has filled 'body' (which is hidden)
			if (!$spam and $this->CI->_check_if_double($data,$table))	$spam=TRUE; // Check if the message is double
			if (!$spam and $this->CI->_check_if_spamtext($data))			$spam=TRUE; // Check the text with FlexyAdmins spam checker
			if ($spam) {
				$errorHtml.='<p class="error">Je reactie is gekenmerkt als spam en wordt niet geplaatst.</p>';
			}
			else {
				// Plaats bericht
				foreach ($data as $field => $value) {$this->CI->db->set($field,$value);}
				$this->CI->db->insert($table);
				// Maak data leeg
				$form->set_data($formData,"Comments");
			}
		}

		// Render form
		$errorHtml.=validation_errors('<div class="error">', '</div>');
		$formHtml=$form->render();
	
		// Get comments
		$this->CI->db->where('id_blog',$item['id']);
		$comments=$this->CI->db->get_results('tbl_comments');
		// make nice date format
		foreach ($comments as $id => $comment) {
			$comments[$id]['niceDate']=strftime('%a %e %b %Y %R',mysql_to_unix($comment['date_date']));
		}

		// Show all
		return $this->CI->view('comments',array('errors'=>$errorHtml,'form'=>$formHtml,'items'=>$comments),true);
	}



	function _setform_fields($table) {
		$fields=$this->CI->db->list_fields($table);
		$formData=array();
		foreach ($fields as $field) {
			$type='input';
			$value='';
			$options=array();
			$validation='required';
			$label=nice_string(remove_prefix($field));
			switch (get_prefix($field)) {
				case 'id':
					$type='';
					break;
				case 'datetime':
					$type='';
					break;
				case 'txt':
					$type='textarea';
					break;
				case 'email':
					$validation='required|valid_email';
					break;
				case 'b':
					$type='checkbox';
					$validation='';
					break;
			}
			if (!empty($type)) $formData[$field]=array('type'=>$type,'label'=>$label,'value'=>$value,'options'=>$options,'validation'=>$validation);
		}
		return $formData;
	}




	// some extra functions to check for spam and double

	function _check_if_robot($data) {
		$robot=false;
		if (!empty($data['spambody'])) $robot=true;
		return $robot;
	}
	function _check_if_double($data,$table) {
		unset($data['spambody']);
		unset($data['datetime_datum']);
		foreach ($data as $field => $value) $this->CI->db->where($field,$value);
		$double=$this->CI->db->get_row($table);
		if ($double) return TRUE;
		return FALSE;
	}
	function _check_if_spamtext(&$data) {
		unset($data['spambody']);
		$spam=new Spam();
		$spam->check_text($data['txt_opmerkingen']);
		$data['int_spamscore']=$spam->get_score();
		if ($spam->get_action()>=2) return true;
		return false;
	}

}



?>