<?

/**
	* Eenvoudig forum
	*
	* Beta!
	*
	* Bestanden
	* ----------------
	*
	* - site/config/forum.php - Hier kun je een een aantal dingen instellen
	* - site/models/forum.php - Model
	* - db/add_forum.sql - database bestand met de benodigde tabellen (TODO)
	* - site/views/forum - De views
	* - site/language/##/forum_lang.php - Taalbestanden
	*
	*
	* @author Jan den Besten
	* @package FlexyAdmin_comments
	*
	*/

class Forum extends Module {

  var $action='';
  var $actions=array('newthread','reply');
  var $user;
  var $offset;

	public function __construct() {
		parent::__construct();
    $this->CI->load->library('pagination');
    $this->offset=$this->CI->uri->get_pagination();
    $this->CI->load->model('forum_model','fm');
    $this->CI->fm->set_limit($this->config('messages_per_page'),$this->offset);
		$this->CI->load->library('user');
    $this->user=$this->CI->user->get_user();
    if (isset($this->user->str_username)) {
      $this->config['user_id']=$this->user->id;
      $this->config['user_name']=$this->user->str_username;
      $this->config['user_lastlogin']=strftime($this->config('datetime_format'),$this->user->last_login);
      $this->CI->fm->set_last_time($this->user->last_login);
    }
    $this->set_module_uri();
		$this->config['uri_new_thread']=$this->config['module_uri'].'/newthread';
		$this->config['uri_reply']=$this->CI->uri->uri_string().'/reply';
    $this->get_uri_args();
    $nr_args=count($this->config['uri_args']);
    if ($nr_args>0) {
      $action=$this->config['uri_args'][count($this->config['uri_args'])-1];
      if (in_array($action,$this->actions)) {
        $this->action=$action;
      }
    }
	}

  /**
  	* Maakt forum menu
  	*
  	* @param bool $add 
  	* @return string
  	* @author Jan den Besten
  	* @ignore
  	*/
  private function forum_menu($add=false) {
    $menu=new Menu();
    $menu->set_current($this->CI->uri->uri_string());
    $menu->set_item_templates('<li %s>','<span class="seperator">|</span></li>');
    $menu->add(array('name'=>lang('menu_index'),'uri'=>remove_suffix($this->config('module_uri'),'/')));
    if (isset($this->config['user_name'])) $menu->add(array('name'=>lang('menu_new_thread'),'uri'=>$this->config('uri_new_thread')));
    if ($add) $menu->add($add);
    $this->config['menu']=$menu->render();
  }

  /**
  	* Roept juiste view aan
  	*
  	* @param string $view 
  	* @param array $data 
  	* @param bool $nop 
  	* @return string
  	* @author Jan den Besten
  	* @ignore
  	*/
  private function view($view,$data,$nop=true) {
    if (!isset($this->config['menu'])) $this->forum_menu();
    $html=$this->CI->view('forum/top',array('config'=>$this->config),true);
    $data=array_merge($data,array('config'=>$this->config));
    $html.=$this->CI->view($view,$data,true);
    return $html;
  }

  /**
  	* Hier wordt de module aangeroepen
  	*
  	* @param string $page
  	* @return string 
  	* @author Jan den Besten
  	*/
	public function index($page) {
    // is there some action?
    if ($this->action) {
      $called=$this->_call_action($page);
      if ($called) return $called;
    }
    $uri_args=$this->config('uri_args');
    $categorie=element(0,$uri_args);
    $thread=element(1,$uri_args);
    
    if ($categorie and $thread) {
      return $this->show_thread($categorie,$thread);
    }
    
    return $this->show_index($page);
	}

  /**
  	* Laat overzicht zien
  	*
  	* @param string $page 
  	* @param string $form[''] 
  	* @return string
  	* @author Jan den Besten
  	*/
  public function show_index($page,$form='') {
    $recent=$this->CI->fm->get_recent_messages();
    $index=$this->CI->fm->get_index();
    return $this->view('forum/index',array('recent'=>$recent,'index'=>$index,'form'=>$form));
  }

  /**
  	* Laat thread zien
  	*
  	* @param string $categorie_uri 
  	* @param string $thread_uri 
  	* @return string
  	* @author Jan den Besten
  	* @ignore
  	*/
  public function show_thread($categorie_uri,$thread_uri) {
    $thread=$this->CI->fm->get_thread_by_uri($thread_uri);
    $pagination='';
    if ($thread['count_messages']<$thread['total_messages'] or $this->offset>0) {
      $config['base_url'] = $this->config('module_uri').'/'.$categorie_uri.'/'.$thread_uri;
      $config['total_rows'] = $thread['total_messages'];
      $config['per_page'] = $this->config('messages_per_page'); 
      $this->CI->pagination->initialize($config); 
      $this->CI->pagination->auto(); 
      $pagination=$this->CI->pagination->create_links();
    }

    $this->config['uri_reply']=str_replace('/reply','',$this->CI->uri->uri_string()).'/reply';
    $form='';
    if (isset($this->config['user_name'])) {
      if ($this->action=='reply' or empty($thread['messages'])) {
        // make sure that on last page, if not redirect to last page
        $last_offset=floor($thread['total_messages']/$this->config('messages_per_page'))*$this->config('messages_per_page');
        if (!empty($pagination) and ($this->offset<$last_offset)) {
          if (has_string('offset',$this->config['uri_reply']))
            $this->config['uri_reply']=preg_replace("/(.*\/offset)(\/\d*\/)(.*)/ui", "$1/".$last_offset."/$3", $this->config['uri_reply']);
          else
            $this->config['uri_reply']=str_replace('/reply', '/offset/'.$last_offset.'/reply', $this->config['uri_reply']);
          // trace_($this->config['uri_reply']);
          redirect($this->config['uri_reply']);
        }
        // show new message form
        $form=$this->form('Reply',$thread['thread']['id']);
      }
      $this->forum_menu(array('uri'=>$this->config('uri_reply'),'name'=>lang('menu_reply')));
    }
    return $this->view('forum/thread',array('thread'=>$thread['thread'], 'messages'=>$thread['messages'], 'pagination'=>$pagination, 'form'=>$form));
  }

  /**
  	* Toont gevraagde form
  	*
  	* @param string $name 
  	* @param int $id[0]
  	* @return string
  	* @author Jan den Besten
  	* @ignore
  	*/
  private function form($name,$id=0) {
		$this->CI->load->library('form');
    $content='';

    switch ($name) {
      case 'Reply':
    		$formData=array( "txt_text"=>array("type"=>"textarea","label"=>lang('form_message'),"validation"=>"required") );
        if ($this->config('allow_attachments')) $formData['file_file']=array('label'=>lang('form_file'), 'type'=>'file');
        break;
      case 'Thread':
        $categories=$this->CI->fm->get_categories();
        $options=array();
        foreach ($categories as $id => $categorie) {
          $options[$categorie['id']]=$categorie['str_title'];
        }
    		$formData=array(  
                          "id_categorie"  =>array("label"=>lang('form_category'),'type'=>'dropdown','options'=>$options,"validation"=>"required"),
                          "str_thread"    =>array("label"=>lang('form_name'),"validation"=>"required")
                        );
        break;
    }
    if (isset($formData)) {
      $formData['txt_spambody']=array("type"=>"textarea",'class'=>'hidden'); // anti spam field
  		$formButtons=array('submit'=>array("submit"=>"submit","value"=>lang('form_submit')));
  		$form=new form($this->CI->uri->uri_string());
  		$form->set_data($formData,$name);
  		$form->set_buttons($formButtons);
  		if ($form->validation()) {
        $data=$form->get_data();
    		$this->CI->load->library('spam');
				// Check for spam
				$spam=FALSE;
				// Check if a robot has filled the empty textarea 'body' (which is hidden)
				if (!$spam and $this->_check_if_robot($data)) $spam=TRUE;
				// Check the text with FlexyAdmins spam checker
				if (!$spam and $this->_check_if_spamtext($data))	$spam=TRUE;

				if ($spam) {
					$content.='<p class="error">SPAM!</p>';
				}
        else {
          // do action
          switch ($name) {
            case 'Reply':
              $thread_id=$id;
              $table_data=array('txt_text'=>$data['txt_text'],'tme_date'=>standard_date('DATE_W3C',time()),'id_user'=>$this->user->id,'id_forum_thread'=>$thread_id);
              // File?
              if ($this->config('allow_attachments') and isset($data['file_file'])) {
      					if (isset($_FILES['file_file']['name']) and !empty($_FILES['file_file']['name']) ) {
      						$this->CI->load->library('upload');
      						$this->CI->load->model('file_manager');
      						$this->CI->file_manager->init( $this->config('attachment_folder'), $this->config('attachment_types') );
      						$result=$this->CI->file_manager->upload_file('file_file');
      						if (!empty($result['file'])) {
                    $file=SITEPATH.'assets/'.$this->config('attachment_folder').'/'.$result['file'];
      							$data['file_file']=$result['file'];
                    $table_data['media_file']=$data['file_file'];
      						}
      					}
              }
              $table='tbl_forum_messages';
							$this->_email_updates($thread_id,$this->config('user_id'),$data['txt_text']);
              $redirect=str_replace('/reply','',$this->CI->uri->uri_string());
              break;
            case 'Thread':
              $table_data=array('str_title'=>$data['str_thread'],'tme_date_created'=>standard_date('DATE_W3C',time()),'id_user'=>$this->user->id,'id_forum_categories'=>$data['id_categorie']);
              $table='tbl_forum_threads';
              $this->CI->load->model('create_uri');
              $table_data['uri']=$this->CI->create_uri->set_table($table)->create($table_data);
              $redirect=$this->config('module_uri').'/'.$this->CI->fm->get_categorie_uri($data['id_categorie']).'/'.$table_data['uri'];
              break;
          }
          if (isset($table) and $table_data) {
            foreach ($table_data as $field => $value) {
              $this->CI->db->set($field,$value);
            }
            $this->CI->db->insert($table);
            redirect($redirect);
          }
        }
      
  		}
  		$validationErrors=validation_errors('<p class="error">', '</p>');
  		if (!empty($validationErrors)) $content.=($validationErrors);
  		$content.=$form->render();
    }
		return $content;
  }

  /**
  	* undocumented function
  	*
  	* @param string $page 
  	* @return bool
  	* @author Jan den Besten
  	* @ignore
  	*/
  private function _call_action($page) {
    $method='_action_'.$this->action;
    if (method_exists($this,$method)) {
      return $this->$method($page);
    }
    return false;
  }

  /**
  	* undocumented function
  	*
  	* @param string $page 
  	* @return string
  	* @author Jan den Besten
  	* @ignore
  	*/
  private function _action_newthread($page) {
    $form=$this->form('Thread');
    return $this->view('forum/new_thread',array('form'=>$form));
  }

  /**
  	* Verzorgt het versturen van emails na een nieuwe post
  	*
  	* @param string $thread_id 
  	* @param string $user_id 
  	* @param string $message['']
  	* @return bool
  	* @author Jan den Besten
  	* @ignore
  	*/
	private function _email_updates($thread_id,$user_id,$message='') {
		$addresses=$this->CI->fm->get_email_adresses_for_thread($thread_id,$this->config('send_mail_to_admin'),$this->config('send_mail_to_thread_users'));
		
		$threadName=$this->CI->fm->get_thread_name($thread_id);
		$threadUri=site_url($this->config('module_uri').'/'.$this->CI->fm->get_thread_uri($thread_id));
		$userName=$this->config('user_name');
		$body=$this->CI->view('forum/'.$this->CI->site['language'].'/new_message.tpl.php',array('thread'=>$threadName,'message'=>$message,'user'=>$userName,'uri'=>$threadUri),true);

		$this->CI->load->library('email');
    $fromMail=$this->CI->site['email_email'];
    $this->CI->email->from($fromMail);
    $this->CI->email->to($fromMail);
		$this->CI->email->bcc($addresses);
		$this->CI->email->subject(langp('notify_email_subject',$this->CI->site['url_url']));
		$this->CI->email->message($body);
    return $this->CI->email->send();
	}



	private function _check_if_robot($data) {
		$robot=false;
		if (!empty($data['txt_spambody'])) $robot=true;
		return $robot;
	}
  // private function _check_if_double($data) {
  //   unset($data['spambody']);
  //   unset($data[$this->config['field_date']]);
  //   foreach ($data as $field => $value) $this->CI->db->where($field,$value);
  //   $double=$this->CI->db->get_row($this->config['table']);
  //   if ($double) return TRUE;
  //   return FALSE;
  // }
	private function _check_if_spamtext(&$data) {
		unset($data['txt_spambody']);
		$spam=new Spam();
    if (isset($data['txt_text'])) $spam->check_text('txt_text');
    if (isset($data['str_thread'])) $spam->check_text('str_thread');
    // $data[$this->config['field_spamscore']]=$spam->get_score();
		if ($spam->get_action()>=2) return true;
		return false;
	}



}

?>