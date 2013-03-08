<?

/**
	* Voegt comments toe aan pagina's
	*
	* Bestanden
	* ----------------
	*
	* - site/config/comments.php - Hier kun je een een aantal dingen instellen (zie hieronder)
	* - db/add_comments.sql - database bestand met de benodigde tabel
	* - site/views/comments.php - De view waarin de comments en het formulier geplaatst worden
	* - site/language/##/comments_lang.php - Taalbestanden
	*
	* Installatie
	* ----------------
	*
	* - Laad het database bestand db/add_comments.sql
	* - Pas de configuratie aan indien nodig (zie: site/config/comments.php)
	* - Pas de view (en styling) aan indien nodig
	* - Maak je eigen taalbestand en/of wijzig de bestaande
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/
 class Comments extends Module {

	private $foreign_table;
	
  /**
   * @ignore
   */
   public function __construct() {
		parent::__construct();
		$this->CI->load->library('form');
		$this->CI->load->library('spam');
		$this->foreign_table=foreign_table_from_key( $this->config('key_id') );
	}
  
  /**
  	* Hier wordt de module standaard aangeroepen
  	*
  	* @param string $page
  	* @return string 
  	* @author Jan den Besten
  	* @ignore
  	*/
	public function index($page) {
		if ( $this->CI->db->table_exists($this->config('table'))) {
      
			// Zet id standaard op 'id', maar als er een samengesteld menu is (res_menu_result) verander de id dan in de id van de originele tabel
			$id=$page['id'];
			if (isset($page['int_id']) and isset($page['str_table']) and $page['str_table']==$this->foreign_table) {
				$id=$page['int_id'];
			}
		
			$formHtml='';
			$errorHtml='';

			// Prepare form fields
      $fields=$this->CI->db->list_fields($this->config('table'));
      $fields=array_combine($fields,array_fill_keys($fields,''));
			$formData=array2formfields($fields,'comments_');
      unset($formData['id']);

			// Set id
			$suffix='__'.$id;
			$formData[$this->config('key_id')]=array('type'=>'hidden','value'=>$id);
			$formData[$this->config('key_id').$suffix]=$formData[$this->config('key_id')];
			unset($formData[$this->config('key_id')]);
			$formData[$this->config('field_date')]['class']='hidden';
			// extra textarea to fake spammers
			$formData['spambody']=array('label'=>'','type'=>'textarea','value'=>'', 'class'=>'hidden');  
			unset($formData['int_spamscore']);


			// Create form
      $form_id='comments_form';
  		$form=new form($this->CI->uri->get(),$form_id);
			$form->set_data($formData,langp('comments_'.'title'));
			$form->set_buttons(array('submit'=>array("submit"=>"submit","value"=>langp('comments_'.'submit'))));
	
			// Validate form, if succes, add comment

			$belongs_to_this=($id== $this->CI->input->post($this->config('key_id').$suffix) );

			if ($form->validation($form_id) and $belongs_to_this) {
				$data=$form->get_data();
				$data[$this->config('key_id')]=$data[$this->config('key_id').$suffix];
				unset($data[$this->config('key_id').$suffix]);
			
				$data[$this->config('field_date')]=date(DATE_ISO8601);

				// Check for spam/double
				$isSpam=$this->CI->spam->check($data);
				$exists=$this->CI->db->has_row($this->config('table'),$data, array('id','spambody',$this->config('field_date')));

				if ($isSpam or $exists) {
					$errorHtml.=langp('comments_'.'spam');
				}
				else {
					// Place comment in database
          unset($data['spambody']);
          $data['int_spamscore']=$this->CI->spam->get_score();
					foreach ($data as $field => $value) {$this->CI->db->set($field,$value);}
					$this->CI->db->insert($this->config('table'));
          
					// Clean form
					$form->set_data($formData,langp('comments_'.'title'));

					// send email that a comment has been placed to the sites owner
					if ($this->config('mail_owner') or $this->config('mail_others')) $this->CI->load->library('email');

					if ($this->config('mail_owner')) {
						$this->CI->email->to( $this->CI->site['email_email'] );
						$this->CI->email->from( $this->CI->site['email_email'] );
						$this->CI->email->subject( langp('comments_'.'mail_to_owner_subject',$this->CI->site['url_url']) );
						$this->CI->email->message( langp('comments_'.'mail_to_owner_body', site_url($this->CI->uri->get())."\n\n".$data[$this->config('field_text')]) );
						if ( ! $this->CI->email->send() )	$errorHtml.=$this->CI->email->print_debugger();
						$this->CI->email->clear();
					}
					if ($this->config('mail_others')) {
						$subject=langp('comments_'.'mail_to_others_subject',$this->CI->site['url_url']);
						$body=langp('comments_'.'mail_to_others_body', site_url($this->CI->uri->get())."\n\n".$data[$this->config('field_text')]);
						$this->CI->db->select( $this->config('field_email') );
						$this->CI->db->where( $this->config('key_id'), $id );
						$emails=$this->CI->db->get_results( $this->config('table') );
						foreach ($emails as $key => $value) {
							$this->CI->email->to( $value[$this->config('field_email')] );
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
			$this->CI->db->where($this->config('key_id'),$id);
			$comments=$this->CI->db->get_results($this->config('table'));
			// make nice date format
			foreach ($comments as $id => $comment) {
				$comments[$id]['niceDate']=strftime('%a %e %b %Y %R',mysql_to_unix($comment[$this->config('field_date')]));
			}

			// Show all
			return $this->CI->view('comments',array('errors'=>$errorHtml,'form'=>$formHtml,'items'=>$comments),true);
		}
		return FALSE;
	}


}



?>