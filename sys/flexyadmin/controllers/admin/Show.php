<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * This Controller shows a grid or form
 *
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Show extends AdminController {
	
	private $form_args;

	public function __construct() {
		parent::__construct();
		$this->load->model("flexy_field","ff");
    $this->load->model('grid_set');
	}

	public function index() {
		$this->_show_all();
	}

	/**
	 * This controls the grid view
	 *
	 * @param string $table Table name
	 * @param mixed $id maybe an id, the last that changed
	 */
	public function grid($table='',$id=false) {
			$args=$this->uri->uri_to_assoc();
			$table=el('grid',$args);
			$id=el('current',$args);
			$info=el('info',$args);
      // $sub=el('sub',$args);
			$offset=el('offset',$args,0);
			$order=el('order',$args);
      $search=$this->input->get('search');
      $where=el('where',$args);
			$this->grid_set->save(array('table'=>$table,'offset'=>$offset,'order'=>$order,'search'=>$search));

			if (!empty($table) and $this->db->table_exists($table)) {
        
        $this->load->model('queu');
        $this->_before_grid($table);
        
        $this->data->table($table);
        
        // Single Row? Laat dan form zien
				$singleRow = ( $this->data->get_setting('max_rows') == 1);
				if ($singleRow) {
          $this->data->table($table);
					$this->data->select( $this->data->get_setting('primary_key') );
					$id=$this->data->get_field($this->data->get_setting('primary_key'));
					$this->form_args['form'] = $table.$this->config->item('URI_HASH').$id;
					return $this->form();
				}
        
        // Rechten voor deze tabel?
        $rights = $this->flexy_auth->has_rights($table,$id);
        if ( !$rights ) {
          $this->_show_all();
          return;
        }
        
        /**
         * Rechten zijn in orde, ga verder
         */
        $this->load->library("pagination");
				$this->load->model("grid");
				$this->lang->load("help");
        
        // Help
				$this->_add_js_variable("help_filter",$this->_add_help(langp('grid_filter')));

        // Table info / UI naam
				$tableInfo=$this->cfg->get('CFG_table',$table);
				$uiTable=$this->ui->get($table);
			
      
				/**
				 * Haal ruwe data op
				 */
        
        $this->data->table( $table );
        $this->data->set_user_id();
        
        // Extra where statement, via Admin Menu instelling?
				if (!empty($info)) {
					$extraInfo   = $this->cfg->get('cfg_admin_menu',$info);
					$extra_where = $extraInfo['str_table_where'];
					if (!empty($extra_where)) {
            $this->data->where( $extra_where, NULL, FALSE) ;
					}
				}

        // Pagination ?
				$pagination=$this->cfg->get("CFG_table",$table,'b_pagination',NULL);
        if ($pagination===NULL) $pagination=$this->config->item('PAGINATION');
				if ($pagination) $pagination=$this->cfg->get('cfg_configurations','int_pagination');
        $pagination=(int)$pagination;
        
        // ORDER
        if ($order) {
					$orderArr=explode(':',$order);
          $last_order = '';
					foreach ($orderArr as $key => $ord) {
            $ordField=trim(trim($ord),'_');
            // if ( $this->data->field_exists($ordField,'grid') ) { // GEEFT PROBLEMEN BIJ VOLGORDE VAN many fields... en het wordt toch al wel gecheck ergens...
							$ordPre=get_prefix($ordField);
							if ($ordField!=='') {
								if ($ordPre=='id' and $ordField!='id') {
                  // Volgorde van een many_to_one abstract veld
                  $ordField = $this->data->get_setting( array('relations','many_to_one', $ordField, 'result_name' ) ) . '.abstract';
								}
								elseif ($ordPre=='tbl') {
                  // Volgorde van een many_to_many abstract veld
                  $ordField = $this->data->get_setting( array('relations','many_to_many', $ordField, 'result_name' ) ) . '.abstract';
                }
                $desc='';
  							if (substr($ord,0,1)=='_') $desc='DESC';
                $this->data->order_by( $ordField, $desc );
                $last_order=add_string($last_order,$ord.' '.$desc,',');
							}
            // }
					}
				}
        else {
          if (!isset($last_order)) $last_order = $this->data->get_setting(array('grid_set','order_by'));
          if (!isset($last_order)) $last_order = $this->data->get_setting('order_by');
          $this->data->order_by($last_order);
          if (has_string('DESC',$last_order)) $last_order='_'.trim(str_replace('DESC','',$last_order));
        }
				
        // Alleen voor gebruiker
				if ( $this->flexy_auth->restricted_id( $table ) ) {
				  $this->data->where_user();
				};
        
        // Maximale lengte voor txt_ velden
        $this->data->select_txt_abstract();
				
				// ZOEKEN?
				if ($search) {
          $extended_search = FALSE;
          if (has_string('{',$search)) {
            $decode_search = json2array($search);
            if ($decode_search) {
              $extended_search = TRUE;
            }
          }
          $search_with = array_keys( $this->data->get_setting( array('grid_set','with') ) );
          if ($extended_search) {
            $search_with=array();
            $this->data->find_multiple( $decode_search, array('with'=>$search_with) );
          }
          else {
            $this->data->find( $search, array(), array('and'=>'OR','with'=>$search_with) );
          }
        }
        
        // Zit er een WHERE in de URL? Pas die dan toe.
        if ($this->config->item('GRID_WHERE') and $where) {
          $where=explode('___',$where);
          foreach ($where as $wh) {
            $wfield=get_prefix($wh,'-');
            $wvalue=get_suffix($wh,'-');
            $this->data->where( $wfield,$wvalue );
          }
        }

        $data_settings = $this->data->get_settings();
        $data = $this->data->get_grid( $pagination, $offset );
        
        $data_query = $this->data->last_query(); //_clean(array('select'=>$table.'.'.PRIMARY_KEY), true);
				$total_rows = $this->data->total_rows();
        
        // trace_($this->data->get_settings());
        // trace_sql($data_query);
        // trace_($last_order);
        // trace_($pagination);
        // trace_($total_rows);
        // trace_($data);
        
				$keys=array();
				if (!empty($data)) $keys=array_keys(current($data));
        $prekeys=get_prefix($keys);
        
        $hasDateField = one_of_array_in_array($this->config->item('DATE_fields_pre'),$prekeys);
        if ($hasDateField) $hasDateField=$keys[$hasDateField];
        $keys=array_combine($keys,$keys);
        
        
        // if datefield and no current: select items from today and set offset of pagination
        if ($this->cfg->get("CFG_table",$table,'b_jump_to_today') and $hasDateField) {
          $this->data->table( $table )->select($hasDateField)->where('DATE(`'.$hasDateField.'`)=DATE(NOW())');
          $today_ids=$this->data->get_result();
          if (empty($today_ids)) {
            $today_ids = $this->data->where('DATE(`'.$hasDateField.'`)>=DATE(NOW())')->order_by($hasDateField)->get_result(1);
          }
          if (!empty($today_ids) and $id=='') {
            $today_ids=array_keys($today_ids);
            // $id=implode('_',$today_ids);
          }
          if ($pagination and $offset=='') {
            $current_id=current($today_ids);
            if (has_string('DESC',$order)) $current_id=end($today_ids);
        		$query=$this->db->query($data_query);
        		$sub_data=$query->result_array();
            $offset=find_row_by_value($sub_data,$current_id,$key=PRIMARY_KEY);
            if ($offset) {
              $offset=key($offset);
              $offset=floor($offset / $pagination) * $pagination;
              if ($offset>0) {
          			$this->grid_set->save(array('table'=>$table,'offset'=>$offset,'order'=>$last_order,'search'=>$search));
                $uri=$this->grid_set->open_uri();
                redirect($uri);
              }
            }
            else {
              $offset=0;
            }
          }
        }

				if (empty($data) and empty($search)) {
					/**
					 * if no data, start an input form
					 */
					$this->form_args['form']=$table.$this->config->item('URI_HASH').'-1';
					$this->form();
					return;
				}
				else 	{
          $html='';
					$grid=new grid();
          $grid->set_editable($this->config->item('GRID_EDIT'));
          
					if ($pagination) {
						$base_url=api_url('API_view_grid',$table);
						$pagination=array('base_url'=>$base_url,'per_page'=>$pagination,'total_rows'=>$total_rows,'offset'=>$offset);
						$grid->set_pagination($pagination);
					}

					/**
					 * if data: first render data, then put data in grid and render as html
					 */

					// remove order fields als geen edit rechten
					if ($rights<RIGHTS_EDIT) {
						foreach ($data as $id => $row) unset($data[$id]['order']);
					}
          
          /**
           * ADD ACTIONS for cfg_users
           */
          if ( $table==='cfg_users' ) {
            $inactive=0;
            $unused=0;
            foreach ($data as $id => $row) {
              if ( $this->flexy_auth->allowed_to_edit_users() ) {
                if ($row['b_active']) {
                  $data[$id]['actions'] = array('send_new_password'=>'users/send_new_password/'.$id);
                }
                else {
                  $inactive++;
                  $data[$id]['actions'] = array('deny'=>'users/deny/'.$id,'accept'=>'users/accept/'.$id);
                }
                if (empty($row['last_login'])) {
                  $unused++;
                  $data[$id]['actions']['invite'] = 'users/invite/'.$id;
                }
              }
            }
            if ($inactive>0) {
              $html.=h(lang('inactive_users'));
              $html.=p() . anchor(api_uri('API_home','users/accept'),lang('accept'),array('class' => 'button')) .' | '. anchor(api_uri('API_home','users/deny'),lang('deny'),array('class' => 'button')) .' '. lang('all_inactive_users').' ('.$inactive.')'._p();
            }
            // Nieuw wachtwoord aan alle geselecteerde gebruikers
            $html.=h(lang('send_new_password'));
            $html.=p() . anchor(api_uri('API_home','users/send_new_password'),lang('send_new_password'),array('class' => 'button selected_users')) .' '. lang('selected_users')._p();
          }
          
					$data=$this->ff->render_grid( $table,$data,$rights, $data_settings, $info);
          
					if (empty($uiTable)) $uiTable=$this->ui->get($table);
					$tableHelp=$this->ui->get_help($table);
					if (!empty($tableHelp)) {
						$uiShowTable=help($uiTable." ",$tableHelp);
					}
					else
						$uiShowTable=$uiTable;
          
					$grid->set_data($data,$uiShowTable);
					$grid->set_order($last_order);
					$grid->set_search($search);

          $searchfields = $this->data->get_setting('fields');
          $searchfields=array_unset_keys($searchfields, array('id','order','self_parent'));
          foreach ($searchfields as $key => $value) {
            unset($searchfields[$key]);
            $searchfields[$value] = $this->ui->get($value,$table);
          }
          $grid->set_searchfields($searchfields);
          
          
					if (!empty($data)) {
						$keys=array_keys(current($data));
						$keys=array_combine($keys,$keys);
					}
          $uiKeys=$this->ui->get($keys,$table);
					$grid->set_headings($uiKeys);
          
          if (is_editable_table($table) AND $rights>=RIGHTS_ADD) {
						$newUri=api_uri('API_view_form',$table.$this->config->item('URI_HASH').'-1');
						if (!empty($info)) $newUri.='/info/'.$info;
						$newIcon=anchor($newUri,help(icon("new"),langp('grid_new',$uiTable)) );
						if ($this->cfg->get('CFG_table',$table,'int_max_rows')<count($data))
							$grid->prepend_to_captions($newIcon,"new");
						else
							$grid->prepend_to_captions('&nbsp;');
					}
          if (is_editable_table($table) AND $rights>=RIGHTS_DELETE)
						$grid->set_heading(PRIMARY_KEY,help(icon("select all"),lang('grid_select_all')).help(icon("delete"),lang('grid_delete'), array("class"=>"delete") ) );
					else {
            // $grid->set_heading(PRIMARY_KEY,'');
          }
					
					if (!empty($id)) $grid->set_current($id);
					$html.=$grid->view("html",$table,"grid");
					$this->_set_content($html);
				}
        $this->_show_type("grid");
			}
			if (!isset($uiTable)) $uiTable="";
			$this->_show_all($uiTable);
		}


/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function form( $table='',$id=false ) {

		if (isset($this->form_args)) {
			$args=$this->form_args;
		}
		else {
			$args=$this->uri->uri_to_assoc();
		}

		$table=el('form',$args);
		$info=el('info',$args);
		$table=explode($this->config->item('URI_HASH'),$table);
		$id=el(1,$table);
		$table=el(0,$table);
    
    // Check of alle gegevens in orde zijn
    if ( empty($table) OR !$this->db->table_exists($table) OR get_prefix($table)==='res' OR $id=='') {
      $this->_show_all();
      return;
    }
    
    // Check of gebruiker rechten heeft
    $rights=$this->flexy_auth->has_rights($table,$id);
    if ( !$rights ) {
      $this->_show_all();
      return;
    }

    // Laad libraries etc
		$this->lang->load("form");
		$this->load->library('upload');
		$this->load->model("order");
		$this->load->helper('html');
		$this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<span class="error">', '<br/></span>');
		$this->load->library("form");
    
    /**
     * Table
     */
    $this->data->table( $table );
		
    // Alleen voor gebruiker
		if ( $this->flexy_auth->restricted_id( $table ) ) {
		  $this->data->where_user();
		};

    // Haal data op
		$data = $this->data->get_form( $id );
    
    // trace_($this->data->last_query());
    // trace_($data);

    /**
     * Opties
     */
    $options=$this->data->get_options('', array_keys($this->data->get_setting( array('form_set','with'))) );

    // trace_($options['groep']);
    // trace_($this->data->get_settings());
    // die();
    
    $data = $this->_before_form($table,$data);
    
		/**
		 * if data: first render data for the form class, then put data in form
		 */
		if ( !empty($data) ) {
      
			$ffData = $this->ff->render_form( $table, $data, $options, $this->data->get_settings() );
      // trace_($ffData['groep']);
      // trace_($ffData['subgroep']);
      
			$actionUri=api_uri('API_view_form',$table.$this->config->item('URI_HASH').$id);
			if (!empty($info)) $actionUri.='/info/'.$info;
			
			$form=new form($actionUri);
      
			$uiTable=$this->ui->get($table);
			$tableHelp=$this->ui->get_help($table);
			if (!empty($tableHelp)) {
				$uiShowTable=help($uiTable,$tableHelp);
			}
			else
				$uiShowTable=$uiTable;
      
			$form->set_data($ffData,$uiShowTable);
			$form->add_password_match();
      $form->hash_passwords();
      
			/**
			 * Validate form, if succes, update/insert data
			 */
			if ($form->validation()) {
				$this->lang->load("update_delete");
				$this->load->model('queu');
        
				$newData=$form->get_data();
				$newData=$this->_after_update($table,$data,$newData);

        $this->data->table( $table )->set_user_id();
        
				if ($id==-1) {
					$id = $this->data->insert( $newData );
          if ($id)
            $this->message->add(langp("insert_new",$table));
          else
            $this->message->add($this->data->get_query_info('error'));
				}
				else {
					$id = $this->data->update( $newData, array( PRIMARY_KEY => $id) );
          if ($id)
            $this->message->add(langp("update_succes",$table));
          else
            $this->message->add_error(langp("update_error",$table));
				}
				
				// Make calls that plugins might have put in the queu
				$this->queu->run_calls();
				// Remove all cached files
				delete_all_cache();

				$redirectUri=$this->grid_set->open_uri($table);
				// trace_($redirectUri);
				if (!empty($info)) $redirectUri.='/info/'.$info;
				if ( $id!==FALSE ) $redirectUri=str_replace('?search=','/current/'.$id.'?search=',$redirectUri);

				redirect($redirectUri);
			}

			/**
			 * Validate form, no succes: show form, maybe with validation errors
			 */
			else {
        $errors=validation_errors();
        if ($errors) {
          $this->message->add($errors);
          $this->message->add(lang('validation_warning'));
        }

				$keys=array_keys($ffData);
				$keys=array_combine($keys,$keys);
				$uiFieldNames=array();
				foreach($keys as $key) {
					$fieldHelp=$this->ui->get_help($table.".".$key);
					if (!empty($fieldHelp))
						$uiFieldNames[$key]=help($this->ui->get($key,$table),$fieldHelp);
					else
						$uiFieldNames[$key]=$this->ui->get($key,$table);
				}
				$form->set_labels($uiFieldNames);
				
				// Fieldsets?
        if ($this->data->get_setting(array('form_set','fieldsets'))) {
  				$fieldsets=array_keys($this->data->get_setting(array('form_set','fieldsets')));  //$this->cfg->get('cfg_table_info',$table,'str_fieldsets');
        }
        if (empty($fieldsets))
          $fieldsets=array();
        elseif (is_string($fieldsets))
          $fieldsets=explode(',',$fieldsets);
        // add default fieldset with name of table
        array_shift($fieldsets);
        array_unshift($fieldsets,$this->ui->get($table));
				$form->set_fieldsets($fieldsets);
				
				if ($rights<RIGHTS_EDIT) $form->no_submit();
				$html=$form->render("html ".$table,$table);
				if ($form->has_htmlfield()) $this->use_editor();
				$this->_add_content($html);
			}
			$this->_show_type("form");
		}

		/**
		 * output
		 */
		
		if (!isset($uiTable)) $uiTable="";
    if (get_prefix($table)=='res') $this->_add_content('(empty)');
		$this->_show_all($uiTable);
	}


/**
 * This controls the user settings
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function user($table='',$id=false) {
    $userId=$this->session->userdata("user_id");
		$this->form_args['form'] = 'cfg_users'.$this->config->item('URI_HASH').$userId;
		return $this->form();
	}


}

?>
