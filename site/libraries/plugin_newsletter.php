<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
	* Aanmaken en versturen van nieuwsbrieven
	*
	* Onderdeel van de module newsletter
	*
	* @author Jan den Besten
	* @package FlexyAdmin_newsletter
	**/

class Plugin_newsletter extends Plugin {

  /**
  	* Wizard object
  	*
  	* @ignore
  	*/
  private $wizard;

  /**
   * @ignore
   */
  public function __construct() {
    parent::__construct();
    $this->CI->load->language('newsletter');
		$this->CI->load->library('form');
		$this->CI->load->library('wizard');

    $this->set_config(array(
      'wizard_create' => array(
        'include_pages'  => array('label'=>lang('include_pages'),'method'=>'_create_include_pages'),
        'edit_text'      => array('label'=>lang('edit_text'),'method'=>'_create_edit_text'),
        'send_test'      => array('label'=>lang('send_test'),'method'=>'_create_send_test')
      ),
      'wizard_send'   => array(
        'send_select'    => array('label'=>lang('send_select'),'method'=>'_send_select'),
        'send_it'        => array('label'=>lang('send_it'),'method'=>'_send_it')
      )
    ));

  }

  /**
   * Toont menu met opties voor het maken, en verzenden van een nieuwsletter en het exporteren van mailadressen
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   */
	public function _admin_api($args=NULL) {
    $action=array_shift($args);
    switch($action) {
      case 'create':
        return $this->_create_newsletter($args);
        break;
      case 'send':
        return $this->_send_newsletter($args);
        break;
      case 'export':
        return $this->_export_addresses($args);
        break;
      default:
        $menu=new Menu();
        $menu->add(array('uri'=>uri_string().'/create','name'=>lang('create_newsletter')));
        $menu->add(array('uri'=>uri_string().'/send','name'=>lang('send_newsletter')));
        $menu->add(array('uri'=>uri_string().'/export','name'=>lang('export_adresses')));
        return $this->view('newsletter/plugin_main',array('title'=>lang('title'),'content'=>$menu->render()) );
    }
  }
   
  /**
   * Maakt en roept de wizard aan
   *
   * @param string $type 
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  private function _wizard($type,$args) {
    $steps=$this->config('wizard_'.$type);
    if (isset($steps) and is_array($steps)) {
      $wizard_config['steps']=$steps;
      $wizard_config['uri_segment']=5;
      $wizard_config['object']=$this;
      $wizard_config['title']=lang($type.'_newsletter');
      $this->wizard=new Wizard($wizard_config);
      array_shift($args);
      return $this->wizard->render().$this->wizard->call_step($args);
    }
    return false;
  }
  
  /**
   * Roep juiste stap aan voor het aanmaken van nieuwsbrief
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
	private function _create_newsletter($args) {
    return $this->_wizard('create',$args);
  }
  
  /**
   * Stap 1 voor het maken van een niewsbrief: kies de inhoud
   *
   * @param string $args 
   * @return string
   * @author Jan den Besten
   * @ignore
   */
  function _create_include_pages($args) {
    if ($this->CI->db->field_exists('dat_date',get_menu_table())) $this->CI->db->where('(CURDATE() - INTERVAL 1 MONTH) <= dat_date');
    $this->CI->db->select('id,order,self_parent,uri,str_title,txt_text');
    $this->CI->db->uri_as_full_uri(true,'str_title');
    $this->CI->db->order_as_tree();
    $pages=$this->CI->db->get_result(get_menu_table());

    $options=array();
    foreach ($pages as $page) {
      $options[$page['id']]=$page['str_title'];
    }
    $formFields = array(  'str_title' => array( 'label'=>lang('subject'), 'validation'=>'required' ),
                          'pages'     => array( 'label'=>lang('add_pages'), 'type'=>'dropdown', 'multiple'=>'multiple', 'options'=>$options )  );
    $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) );
		$form=new form(uri_string());
		$form->set_data($formFields, lang('create_newsletter') );
		$form->set_buttons($formButtons);
    
    if ($form->validation()) {
      $data=$form->get_data();
      $pageIDs=explode('|',$data['pages']);
      foreach ($pages as $id=>$page) {
        if (in_array($id,$pageIDs))
          $pages[$id]['txt_text']=intro_string($pages[$id]['txt_text'],$this->config('intro_length'),'CHARS',$this->config('allowed_tags'));
        else
          unset($pages[$id]);
      }
      $unsubmit='./'; //$this->CI->find_module_uri('newsletter'); // ##
      if (!empty($unsubmit)) $unsubmit.='?unsubmit';
      
      $body=$this->CI->load->view('newsletter/newsletter.tpl.php',array('pages'=>$pages,'base_url'=>base_url(),'unsubmit'=>$unsubmit),true);
      $this->CI->db->set('str_title',$data['str_title']);
      $this->CI->db->set('dat_date',standard_date('DATE_W3C',now()));
      $this->CI->db->set('txt_text',$body);
      $this->CI->db->insert('tbl_newsletters');
      $newsletterID=$this->CI->db->insert_id();
      $redirect=site_url($this->wizard->get_next_step_uri($newsletterID));
      // trace_($redirect);
      redirect($redirect);
    }
    else {
      return $this->view('newsletter/plugin_main',array('title'=>lang('create_newsletter'),'content'=>validation_errors('<p class="error">', '</p>').$form->render()));
    }
  }

  /**
   * Stap 2 van nieuwsbrief maken: Pas tekst aan
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  function _create_edit_text($args) {
    $id=element(0,$args);
    if ($id) {
  		$this->CI->db->where('id',$id);
      $this->CI->db->select('id,dat_date,str_title,txt_text');
  		$newsletter=$this->CI->db->get_row('tbl_newsletters');
      $form=new Form();
      $formData=array2formfields($newsletter);
      $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) );
      $form->set_data($formData,lang('edit_newsletter'));
  		$form->set_buttons($formButtons);
      if ($form->validation()) {
        $data=$form->get_data();
        $this->CI->db->set('str_title',$data['str_title']);
        $this->CI->db->set('dat_date',$data['dat_date']);
        $this->CI->db->set('txt_text',$data['txt_text']);
        $this->CI->db->where('id',$data['id']);
        $this->CI->db->update('tbl_newsletters');
        $redirect=site_url($this->wizard->get_next_step_uri($data['id']));
        redirect($redirect);
      }
      else {
        $errors=validation_errors('<p class="error">', '</p>');
        return $this->view('newsletter/plugin_main',array('title'=>lang('create_newsletter'),'content'=>$errors.$form->render() ));
      }
    }
	}

  /**
   * Stap 3 voor maken van nieuwsbrief: stuur een test
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
   public function _create_send_test($args) {
    $id=element(0,$args);
    if ($id) {
  		$this->CI->db->where('id',$id);
      $this->CI->db->select('id,dat_date,str_title,txt_text');
  		$newsletter=$this->CI->db->get_row('tbl_newsletters');
      $form=new Form();
      $mail['to']=$this->CI->db->get_field('tbl_site','email_email');
      $mail['from']=$mail['to'];
      $mail['subject']=$newsletter['str_title'];
      $mail['txt_body']=$newsletter['txt_text'];
      $formData=array2formfields($mail);
      $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('send')) );
      $form->set_data($formData,lang('send_test'));
  		$form->set_buttons($formButtons);
      if ($form->validation()) {
        $mail=$form->get_data();
        $mail['body']=$mail['txt_body'];
        unset($mail['txt_body']);
        $rapport=$this->_send_mail($mail);
        $this->add_to_rapport($id,$rapport);
        return $this->view('newsletter/plugin_main',array('title'=>lang('send_newsletter'),'content'=>$rapport ));
      }
      else {
        $errors=validation_errors('<p class="error">', '</p>');
        return $this->view('newsletter/plugin_main',array('title'=>lang('send_newsletter'),'content'=>$errors.$form->render() ));
      }
    }
  }
   
  /**
   * Roept juiste stap aan voor verzenden van nieuwsbrief
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  private function _send_newsletter($args) { 
    return $this->_wizard('send',$args);
  }
  
  /**
   * Stap 1 voor verzending: Selecteer de nieuwsbrief
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  public function _send_select($args) {
    $newsletters=$this->CI->db->get_result('tbl_newsletters');
    $options=array();
    foreach ($newsletters as $newsletter) {
      $options[$newsletter['id']]=$newsletter['dat_date'].' - '.$newsletter['str_title'];
    }
    $formFields = array( 'newsletter' => array( 'label'=>lang('choose_newsletter'), 'type'=>'dropdown', 'options'=>$options, 'validation'=>'required' )  );
    $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) );
		$form=new form(uri_string());
		$form->set_data($formFields, lang('choose_newsletter') );
		$form->set_buttons($formButtons);
    if ($form->validation()) {
      $data=$form->get_data();
      $redirect=site_url($this->wizard->get_next_step_uri($data['newsletter']));
      redirect($redirect);
    }
    else {
      $errors=validation_errors('<p class="error">', '</p>');
      return $this->view('newsletter/plugin_main',array('title'=>lang('send_newsletter'),'content'=>$errors.$form->render() ));
    }
  }

  /**
   * Stap 2 voor verzending nieuwsbrief: verstuur
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  public function _send_it($args) {
    $id=element(0,$args);
    if ($id) {
  		$this->CI->db->where('id',$id);
      $this->CI->db->select('id,dat_date,str_title,txt_text');
  		$newsletter=$this->CI->db->get_row('tbl_newsletters');
      $form=new Form();
      $mail['to']=$this->CI->db->get_field('tbl_site','email_email');
      $mail['bcc']=$this->_get_adresses();
      $mail['from']=$mail['to'];
      $mail['subject']=$newsletter['str_title'];
      $mail['txt_body']=$newsletter['txt_text'];
      $formData=array2formfields($mail);
      $formButtons = array( 'submit'=>array('submit'=>'submit', 'value'=>lang('send')) );
      $form->set_data($formData,lang('send_it'));
  		$form->set_buttons($formButtons);
      if ($form->validation()) {
        $mail=$form->get_data();
        $mail['body']=$mail['txt_body'];
        unset($mail['txt_body']);
        $rapport=$this->_send_mail($mail);
        $this->add_to_rapport($id,$rapport);
        return $this->view('newsletter/plugin_main',array('title'=>lang('send_newsletter'),'content'=>'<h2>Result</h2>'.$rapport ));
      }
      else {
        $errors=validation_errors('<p class="error">', '</p>');
        return $this->view('newsletter/plugin_main',array('title'=>lang('send_newsletter'),'content'=>$errors.$form->render() ));
      }
    }
  }


  /**
   * Exporteer adressen
   *
   * @param string $args 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  private function _export_addresses($args) {
    $this->add_message('Kopier alle adressen van hieronder naar je emailprogramma.');
    return $this->view('newsletter/plugin_export',array('title'=>lang('export_adresses'),'adresses'=>$this->_get_adresses()));
		$this->add_content('<p><textarea style="width:590px" rows="20">'.$adressen.'</textarea></p>');
  }


  /**
   * Verstuur een mail
   *
   * @param string $mail 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  private function _send_mail($mail) {
    $rapport='<p>'.strftime('%a %d %b %Y %T',now()).' - ';
    $mail['body']=$this->_prepare_body($mail['body']);
    $this->CI->email->clear(TRUE);
    if ($this->config('send_one_by_one')) {
      $send_to=array();
      $to=explode(',',$mail['to']);
      $bcc=explode(',',$mail['bcc']);
      unset($mail['bcc']);
      // TO
      foreach ($to as $to_one) {
        $mail['to']=$to_one;
        $this->CI->email->set_mail($mail);
        $send=$this->CI->email->send();
        $this->CI->email->clear(TRUE);
    		if ($send)
    			$send_to[]=$to_one;
    		else {
          $rapport.='ERROR sending ('.$to_one.'), debug information:<br/>';
    			$rapport.=$this->CI->email->print_debugger();
        }
        $mail['to']='';
      }
      // BCC
      foreach ($bcc as $to_one) {
        $mail['bcc']=$to_one;
        $this->CI->email->set_mail($mail);
        $send=$this->CI->email->send();
        $send=TRUE;
        $this->CI->email->clear(TRUE);
    		if ($send)
    			$send_to[]=$to_one;
    		else {
          $rapport.='ERROR sending ('.$to_one.'), debug information:<br/>';
    			$rapport.=$this->CI->email->print_debugger();
        }
      }
      $rapport.='Send to '.count($send_to).' email-address(es)<br/>';
    }
    else {
      $this->CI->email->set_mail($mail);
      $send=$this->CI->email->send();
  		if ($send)
  			$rapport.='Send to '.$this->CI->email->get_total_send_addresses().' email-address(es)';
  		else {
        $rapport.='ERROR sending, debug information:<br/>';
  			$rapport.=$this->CI->email->print_debugger();
      }
    }
    
    $rapport.='</p>';
    return $rapport;
  }
  
  /**
   * Voegt tekst aan rapportage toe
   *
   * @param string $id 
   * @param string $rapport 
   * @return void
   * @author Jan den Besten
   * @ignore
   */
  private function add_to_rapport($id,$rapport) {
    $old_rapport=$this->CI->db->get_field('tbl_newsletters','txt_rapport',$id);
    $rapport=$old_rapport.$rapport;
    $this->CI->db->set('txt_rapport',$rapport);
    $this->CI->db->where('id',$id);
    $this->CI->db->update('tbl_newsletters');
  }

	
  /**
   * Zorgt voor juiste verwijzingingen in de tekst van een mailbody
   *
   * @param string $body 
   * @return string
   * @author Jan den Besten
   * @ignore
   */
  private function _prepare_body($body) {
    // good paths to local images
		$body=str_replace('src="','src="'.base_url(),$body);
    // good internal links
		$body=str_replace('href="mailto:','##MAIL##',$body);
		$body=str_replace('href="undefined/','href="'.base_url(),$body);
		$body=preg_replace('/href=\"(?!http:\/\/).*?/','href="'.base_url(),$body);
		$body=str_replace('##MAIL##','href="mailto:',$body);
    //
    return $body;
  }

  /**
   * Haalt alle adressen op
   *
   * @return array
   * @author Jan den Besten
   * @ignore
   */
  private function _get_adresses() {
    $adresses=$this->CI->db->get_result( $this->config('send_to_address_table') );
    $to='';
		foreach ($adresses as $adres) {
      $mail=$adres[$this->config('send_to_address_field')];
      $name=$adres[$this->config('send_to_name_field')];
      if (empty($name))
        $a=$mail;
      else
        $a=$name.' <'.$mail.'>';
      $to=add_string($to,$a,', ');
    }
    return $to;
  }


  /**
   * @ignore
   * @depricated
   */
   public function get_show_type() {
		return 'form';
	}
  
  
  
  
}

?>