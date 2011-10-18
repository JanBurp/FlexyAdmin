<?

/*

Add comments to a page

Config:		site/config/comments.php
View:			site/views/comments.php
Database: db/add_comments.sql

Change the field id_menu in db & config to something else if you use the comments on something other than the menu.

*/

class Comments extends Module {

	var $foreign_table;
	
	public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->library('spam');
		$this->load_config('comments');
		$this->set_language();
		$this->foreign_table=foreign_table_from_key( $this->config['key_id'] );
	}

	public function set_language($lang='') {
		if (empty($lang))	$this->config['language']=$lang;
		if (empty($lang))	$this->config['language']=$this->CI->site['language'];
	}

	private function get_text($name,$s='') {
		if (isset($this->config[$this->config['language']][$name]))
			$text=$this->config[$this->config['language']][$name];
		else
			$text='';
		return str_replace('%s',$s,$text);
	}

	public function index($item) {
		if ( $this->CI->db->table_exists($this->config['table'])) {
			// Get id for current item where comments belong to
			$id=$item['id'];
			if (isset($item['int_id']) and isset($item['str_table']) and $item['str_table']==$this->foreign_table) {
				// trace_($item);
				$id=$item['int_id'];
			}
		
			$formHtml='';
			$errorHtml='';

			// Prepare form fields
			$formData=array();
			$formData=$this->_setform_fields();
			// Set id
			$suffix='__'.$id;
			$formData[$this->config['key_id']]=array('type'=>'hidden','value'=>$id);
			$formData[$this->config['key_id'].$suffix]=$formData[$this->config['key_id']];
			unset($formData[$this->config['key_id']]);
			$formData[$this->config['field_date']]['class']='hidden';
			// extra textarea to fake spammers
			$formData['spambody']=array('label'=>'','type'=>'textarea','value'=>'', 'class'=>'hidden');  
			unset($formData['int_spamscore']);

			// Create form
			$form=new form($this->CI->uri->get());
			$form->set_data($formData,$this->get_text('title'));
			$form->set_buttons(array('submit'=>array("submit"=>"submit","value"=>$this->get_text('submit'))));
	
			// Validate form, if succes, add comment

			$belongs_to_this=($id== $this->CI->input->post($this->config['key_id'].$suffix) );

			if ($form->validation() and $belongs_to_this) {
				$data=$form->get_data();
				$data[$this->config['key_id']]=$data[$this->config['key_id'].$suffix];
				unset($data[$this->config['key_id'].$suffix]);
			
				$data[$this->config['field_date']]=date(DATE_ISO8601);

				// Check for spam
				$spam=FALSE;
				// Check if a robot has filled the empty textarea 'body' (which is hidden)
				if (!$spam and $this->_check_if_robot($data)) $spam=TRUE;
				// Check if the message is double
				if (!$spam and $this->_check_if_double($data,$this->config['table']))	$spam=TRUE;
				// Check the text with FlexyAdmins spam checker
				if (!$spam and $this->_check_if_spamtext($data))	$spam=TRUE;

				if ($spam) {
					$errorHtml.=$this->get_text('spam');
				}
				else {
					// Place comment in databas
					foreach ($data as $field => $value) {$this->CI->db->set($field,$value);}
					$this->CI->db->insert($this->config['table']);
					// Clean form
					$form->set_data($formData,$this->get_text('title'));

					// send email that a comment has been placed to the sites owner
					if ($this->config['mail_owner'] or $this->config['mail_others']) $this->CI->load->library('email');

					if ($this->config['mail_owner']) {
						$this->CI->email->to( $this->CI->site['email_email'] );
						$this->CI->email->from( $this->CI->site['email_email'] );
						$this->CI->email->subject( $this->get_text('mail_to_owner_subject',$this->CI->site['url_url']) );
						$this->CI->email->message( $this->get_text('mail_to_owner_body', site_url().$this->CI->uri->get())."\n\n".$data[$this->config['field_text']] );
						if ( ! $this->CI->email->send() )	$errorHtml.=$this->CI->email->print_debugger();
						$this->CI->email->clear();
					}
					if ($this->config['mail_others']) {
						$subject=$this->get_text('mail_to_others_subject',$this->CI->site['url_url']);
						$body=$this->get_text('mail_to_others_body', site_url().$this->CI->uri->get())."\n\n".$data[$this->config['field_text']];
						$this->CI->db->select( $this->config['field_email'] );
						$this->CI->db->where( $this->config['key_id'], $id );
						$emails=$this->CI->db->get_results( $this->config['table'] );
						foreach ($emails as $key => $value) {
							$this->CI->email->to( $value[$this->config['field_email']] );
							$this->CI->email->from( $this->CI->site['email_email'] );
							$this->CI->email->subject( $subject );
							$this->CI->email->message( $body );
							if ( ! $this->CI->email->send() )	$errorHtml.=$this->CI->email->print_debugger();
							$this->CI->email->clear();
						}
					}
				}
			}

			// Render form
			if ($belongs_to_this)
				$errorHtml.=validation_errors('<div class="error">', '</div>');
			else {
				// clean values for others
				foreach ($formData as $key => $value) {
					$pre=get_prefix($key);
					if ($pre!='id')	$formData[$key]['value']='';
				}
				$form->set_data($formData);
			}
			$formHtml=$form->render();
	
			// Get comments
			$this->CI->db->where($this->config['key_id'],$id);
			$comments=$this->CI->db->get_results($this->config['table']);
			// make nice date format
			foreach ($comments as $id => $comment) {
				$comments[$id]['niceDate']=strftime('%a %e %b %Y %R',mysql_to_unix($comment[$this->config['field_date']]));
			}

			// Show all
			return $this->CI->view('comments',array('errors'=>$errorHtml,'form'=>$formHtml,'items'=>$comments,'lang'=>$this->config[$this->config['language']]),true);
		}
		return FALSE;
	}



	private function _setform_fields() {
		$fields=$this->CI->db->list_fields($this->config['table']);
		$formData=array();
		foreach ($fields as $field) {
			// standard attributes
			$type='input';
			$value='';
			$options=array();
			$validation='required';
			// label
			$label=$this->get_text($field);
			if (empty($label)) $label=nice_string(remove_prefix($field));
			
			// special attributes for some fields
			if ($field==$this->config['field_date']) $type='';
			
			switch (get_prefix($field)) {
				case 'id':
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

	private function _check_if_robot($data) {
		$robot=false;
		if (!empty($data['spambody'])) $robot=true;
		return $robot;
	}
	private function _check_if_double($data) {
		unset($data['spambody']);
		unset($data[$this->config['field_date']]);
		foreach ($data as $field => $value) $this->CI->db->where($field,$value);
		$double=$this->CI->db->get_row($this->config['table']);
		if ($double) return TRUE;
		return FALSE;
	}
	private function _check_if_spamtext(&$data) {
		unset($data['spambody']);
		$spam=new Spam();
		$spam->check_text($this->config['field_text']);
		$data[$this->config['field_spamscore']]=$spam->get_score();
		if ($spam->get_action()>=2) return true;
		return false;
	}

}



?>