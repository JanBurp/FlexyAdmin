<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class Plugin_automenu extends Plugin_ {

	var $menuTable;
	var $automationTable;
	var $resultMenu;
	
	var $newMenu;
	var $lastId;
	var $parentIDs;
	var $languages;
	
	var $automationData;

	function init($init=array()) {
		parent::init($init);
		// Fill here on which trigger the plugin must act
		$this->automationTable='cfg_auto_menu';
		if ($this->db->table_exists($this->automationTable)) {
			$this->menuTable=$this->cfg->get('CFG_configurations','str_menu_table');
			$this->resultMenu='res_menu_result';
			$checkTables=array();
			$checkTables[]=$this->menuTable;
			$checkTables[]=$this->automationTable;
			$checkTables[]=$this->resultMenu;
			// get tables from automation data
			$this->db->order_as_tree();
			$this->automationData=$this->db->get_results($this->automationTable);
			// trace_($this->automationData);
			foreach ($this->automationData as $aData) {
				if (!in_array($aData['table'], $checkTables))	$checkTables[]=$aData['table'];
				$foreignTable=$aData['field_group_by'];
				$foreignTable=explode('.',$foreignTable);
				if (isset($foreignTable[1])) {
					$foreignTable=$foreignTable[1];
					$foreignTable=foreign_table_from_key($foreignTable);
					if (!in_array($foreignTable, $checkTables))	$checkTables[]=$foreignTable;
				}
			}
			$this->act_on(array('existingTables'=>'res_menu_result','tables'=>implode(',',$checkTables)));
		}
		else
			$this->act_on();
	}


	function _after_update() {
		$this->_create_auto_menu();
		return FALSE;
	}

	function _after_delete() {
		$this->_create_auto_menu();
		return FALSE;
	}

	function _admin_api($args=NULL) {
		$this->_add_content(h($this->plugin,1));
		$this->init();
		$this->_create_auto_menu();
		$this->_add_content('<p>Menu reset ready.');
	}
	
	
	function _create_auto_menu() {
		$lastOrder=0;
		$this->newMenu=array();
		$this->parentIDs=array();
		$this->lastId=1;

		// Loop through all options in Auto Menu
		
		foreach ($this->automationData as $autoKey => $autoValue) {
			
			switch($autoValue['str_type']) {
				

				case 'menu item':
					$name=$autoValue['str_description'];
					$uri=safe_string($name);
					$item=array('uri'=>$uri,'str_title'=>$name);
					$item=$this->_setResultMenuItem($item,true);
					$this->_insertItem($item);
					// $this->_moveChildren();
					break;

				
				case 'from menu table':
					$data=$this->db->get_results($autoValue['table']);
					foreach ($data as $item) {
						$item=$this->_setResultMenuItem($item,true);
						$item['str_table']=$autoValue['table'];
						$item['str_uri']=$item['uri'];
						$item['int_id']=$item['id'];
						$this->_insertItem($item);
					}
					$this->_moveChildren();
					break;
		
		
				case 'from submenu table':
					if (isset($autoValue['str_where']) and !empty($autoValue['str_where'])) {
						$this->db->where($autoValue['str_where']);
					}
					$limit=0;
					if (isset($autoValue['int_limit'])) {
						$limit=$autoValue['int_limit'];
					}
					$data=$this->db->get_results($autoValue['table'],$limit);
					$order=0;
					$parent=0;
					if (!empty($autoValue['str_parent_where'])) {
						$parent=$this->_get_where_parent($autoValue);
					}
					// trace_($parent);
					// trace_($data);
					// is er al een sub? Gebruik die order
					$sub=$this->newMenu;
					$sub=find_row_by_value($sub,$parent,'self_parent');
					if ($sub) {
						$sub=current($sub);
						$order=$sub['order']+1;
					} 

					// stop ze er eerst allemaal in met zelfde parent en onthou ondertussen dat ze eventueel andere parent moeten krijgen
					$parIDs=array(); // array met id's die andere parent moeten krijgen
					$oldIDs=array(); // onthou hier de originele id's
					foreach ($data as $item) {
						$this->_setResultMenuItem($item);
						$item['str_table']=$autoValue['table'];
						$item['str_uri']=$item['uri'];
						$item['int_id']=$item['id'];
						if (!isset($item['self_parent'])) $item['self_parent']=0;
						if (isset($parent) and $item['self_parent']==0) {
							$item['self_parent']=$parent;
						}
						elseif ($item['self_parent']>0) {
							$item['old_parent']=$item['self_parent'];
							$item['self_parent']=$parent;
						}
						if (!isset($item['order'])) $item['order']=$order++;
						if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
							if (isset($this->newMenu[$item['self_parent']]['str_module'])) {
								$parentModule=$this->newMenu[$item['self_parent']]['str_module'];
								if (isset($item['str_module']))
									$item['str_module'].=' '.$parentModule;
								else
									$item['str_module']=$parentModule;
							}
						}
						$item=$this->_insertItem($item);
						if (isset($item['old_parent'])) {
							$parIDs[$item['id']]=$item['old_parent'];
							unset($item['old_parent']);
						}
						$oldIDs[$item['int_id']]=$item['id'];
					}
					// ok, verplaats nu naar goede parents nu ze allemaal bekend zijn
					foreach ($parIDs as $id => $oldParent) {
						$newParent=$oldIDs[$oldParent];
						$this->newMenu[$id]['self_parent']=$newParent;
					}
					break;
		
		
				case 'from category table':
					$data=$this->db->get_result($autoValue['table']);
					$self_parents=array();
					if (!empty($autoValue['str_parent_uri'])) {
						// On multiple places?
						if (strpos($autoValue['str_parent_uri'],'/*')!==false) {
							$topUri=str_replace('/*','',$autoValue['str_parent_uri']);
							$topItem=find_row_by_value($this->newMenu,$topUri,'uri');
							$topItem=current($topItem);
							$parentItems=find_row_by_value($this->newMenu,$topItem['id'],'self_parent');
						}
						else {
							$parentItems=find_row_by_value($this->newMenu,$autoValue['str_parent_uri'],'uri');
						}
						$self_parents=array_keys($parentItems);
					}
					$lastOrder=0;
					if (empty($self_parents)) {
						$self_parents[]=0;
						$last=find_row_by_value($this->newMenu,0,'self_parent');
						if ($last) {
							$last=sort_by($last,'order',TRUE);
							$last=current($last);
							$lastOrder=$last['order']+1;
						}
						else {
							$lastOrder=0;
						}
					}
					foreach ($data as $key=>$item) {
						foreach ($self_parents as $self_parent) {
							$item=$this->_setResultMenuItem($item);
							$item['order']=$lastOrder++;
							$item['self_parent']=$self_parent;
							$item['str_table']=$autoValue['table'];
							$item['str_uri']=$item['uri'];
							$item['int_id']=$item['id'];
							if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
								if (isset($this->newMenu[$item['self_parent']]['str_module'])) {
									$parentModule=$this->newMenu[$item['self_parent']]['str_module'];
									if (isset($item['str_module']))
										$item['str_module'].=' '.$parentModule;
									else
										$item['str_module']=$parentModule;
								}
							}
							$this->_insertItem($item);
						}
					}
					break;
		
		
				case 'from table group by category':
					// trace_($autoValue);
					$pagination=(int)$autoValue['str_parameters'];
					// check if table is many table
					$fromRel=false;
					$preTable=get_prefix($autoValue['field_group_by']);
					if ($preTable=='rel') {
						// yes many table
						$fromRel=true;
						$groupField=get_suffix($autoValue['field_group_by'],'.');
						$groupTable='tbl_'.remove_prefix($groupField);
					}
					else {
						// foreign table
						$groupField=remove_prefix($autoValue['field_group_by'],'.');
						$groupTable=foreign_table_from_key($groupField);
					}
					$groupData=$this->db->get_result($groupTable);
					
					foreach ($groupData as $groupId=>$groupData) {
						$titleField='str_title';
						if (!isset($groupData[$titleField])) {
							$possibleFields=array_keys($groupData);
							$possibleFields=filter_by($possibleFields,'str_title');
							$titleField=current($possibleFields);
						}
						if ($fromRel) {
							$this->db->add_many();
							$this->db->where($autoValue['field_group_by'],$groupId);
						}
						else {
							$this->db->where($autoValue['field_group_by'],$groupId);
						}
						$data=$this->db->get_result($autoValue['table']);
						
						// trace_('#SHOW# '.$this->db->ar_last_query);
						// trace_($groupId);
						// trace_($groupData);
						
						if ($data) {
							// trace_('Pagination: '.$pagination);
							// trace_('Count Data:'.count($data));
							$parentData=find_row_by_value($this->newMenu,$groupData[$titleField],$titleField);
							$parentData=current($parentData);
							// trace_($parentData);
							$selfParent=$parentData['id'];
							$lastOrder=0;
							$subData=find_row_by_value($this->newMenu,$selfParent,'self_parent');
							if ($subData) {
								// lastOrder is not 0
								$subData=array_slice($subData,count($subData)-1);
								$subData=current($subData);
								$lastOrder=$subData['order']+1;
							}
							if ($pagination) {
								$subOrder=$lastOrder;
								$lastOrder=0;
								$subSelfParent=$selfParent;
							}
							$nr=0;
							foreach ($data as $item) {
								if ($pagination and ($nr%$pagination==0)) {
									// add subpage if needed
									$page=round($nr/$pagination)+1;
									// trace_('Add subpage ['.$page.']');
									$self=$this->_insertItem( array('uri'=>$page, $groupData[$titleField]=>$page, 'order'=>$subOrder++, 'self_parent'=>$subSelfParent) );
									$selfParent=$self['id'];
								}
								$nr++;
								
								$this->_setResultMenuItem($item);
								$item['order']=$lastOrder++;
								$item['self_parent']=$selfParent;
								$item['str_table']=$autoValue['table'];
								$item['str_uri']=$item['uri'];
								$item['int_id']=$item['id'];
								if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
									if (isset($this->newMenu[$item['self_parent']]['str_module'])) {
										$parentModule=$this->newMenu[$item['self_parent']]['str_module'];
										if (isset($item['str_module']))
											$item['str_module'].=' '.$parentModule;
										else
											$item['str_module']=$parentModule;
									}
								}
								$this->_insertItem($item);
							}
						}
					}
					break;
					
					
				case 'split by language':
					$this->languages=$autoValue['str_parameters'];
					$this->languages=explode('|',$this->languages);
					$order=0;
					$beforeMenu=$this->newMenu;
					foreach ($this->languages as $lang) {
						// add language
						$item=array('uri'=>$lang,'order'=>$order++,'self_parent'=>-1,'str_title'=>$lang,'str_title_'.$lang=>$lang);
						if ($order==1) {
							$item=$this->_insertItem($item,1);
							$langID=$item['id'];
							// first language, just move current menu under it
							foreach ($this->newMenu as $id => $item) {
								if ($id!=$langID and $item['self_parent']==0)	$this->newMenu[$id]['self_parent']=$langID;
							}
						}
						else {
							$item=$this->_insertItem($item);
							$langID=$item['id'];
						 	$this->_addBranch($item,$beforeMenu);
						}
					}
					break;
					
				
				case 'by module':
					// call module
					$module=$autoValue['str_parameters'];
					$moduleFile=SITEPATH.'libraries/'.$module.'.php';
					if (file_exists($moduleFile)) {
						include_once($moduleFile);						// if function exists, call it
						$moduleFunction=$module;
						if (function_exists($moduleFunction)) {
							$items=$moduleFunction($autoValue);
							// Produce menu from returned items
							if ($items) {
								$order=0;
								$parent=$this->_get_where_parent($autoValue);
								if (isset($autoValue['b_keep_parent_modules']) and $autoValue['b_keep_parent_modules']) {
									if (isset($this->newMenu[$parent]['str_module'])) {
										$parentModule=$this->newMenu[$parent]['str_module'];
									}
								}
								foreach ($items as $item) {
									$item=$this->_setResultMenuItem($item,true);
									$item['self_parent']=$parent;
									$item['order']=$order;
									$order++;
									if (isset($parentModule)) $item['str_module']=$parentModule;
									$this->_insertItem($item);
								}
							}
						}
					}
					break;	
			}
			
		}

		// change some things
		foreach ($this->newMenu as $id => $item) {
		  // if self_parent -1 (language) replace with 0
			if (isset($item['self_parent']) and $item['self_parent']==-1) $item['self_parent']=0;
			$languages=$this->languages;
			if (empty($languages)) {
				$languages[]='';
			}
			foreach ($languages as $lang) {
				if (!empty($lang)) $lang='_'.$lang;
				if (!isset($item['str_title'.$lang])) $item['str_title'.$lang]='';
				if (isset($item['uri']) and empty($item['str_title'.$lang])) $item['str_title'.$lang]=$item['uri'];
			}
			$this->newMenu[$id]=$item;
		}
		ksort($this->newMenu);

		// put in db
		$this->db->trans_start();
		
		$this->db->truncate($this->resultMenu);
		$fields=$this->db->list_fields($this->resultMenu);
		$lang='';
		foreach ($this->newMenu as $row) {
			if (isset($row['self_parent']) and isset($this->languages) and in_array($row['uri'],$this->languages)) $lang=$row['uri'];
			foreach ($row as $field => $value) {
				if (in_array($field,$fields)) {
					$this->db->set($field,$value);
				}
				elseif ($lang!='') {
					$post=get_suffix($field);
					$langField=str_replace('_'.$lang,'',$field);
					if (in_array($langField,$fields)) {
						$this->db->set($langField,$value);
					}
				}
			}
			$this->db->insert($this->resultMenu);
		}
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			trace_('Sorry, transaction error');
		}

		// update linklist etc
		$this->editor_lists->create_list("links");
	}


	function _get_where_parent($autoValue) {
		// parse where...
		$whenParser=preg_split('/\s*(<>|!=|=|>|<)\s*/',$autoValue['str_parent_where'],-1,PREG_SPLIT_DELIM_CAPTURE);
		// TODO: only '=' operator works now
		$parent=find_row_by_value($this->newMenu,str_replace(array('"',"'"),'',$whenParser[2]),$whenParser[0]);
		if ($parent) {
			$parent=current($parent);
			$parent=$parent['id'];
		}
		return $parent;
	}

	function _setResultMenuItem($item,$setId=false) {
		// if (!$setId) {
		// 	if (isset($item['id']))						unset($item['id']);
		// 	if (isset($item['order']))				unset($item['order']);
		// 	if (isset($item['self_parent']))	unset($item['self_parent']);
		// }
		// foreach ($item as $key => $value) {
			// if (!$this->db->field_exists($key,$this->resultMenu)) unset($item[$key]);
		// }
		return $item;
	}
	
	function _insertItem($item,$id='') {
		if (empty($id)) {
			$this->lastId++;
			if (!isset($item['id'])) $item['id']=$this->lastId;
			$this->parentIDs[$item['id']]=$this->lastId;
			$item['id']=$this->lastId;
			$this->newMenu[$this->lastId]=$item;
		}
		else {
			$item['id']=$id;
			$this->newMenu[$id]=$item;
		}
		return $item;
	}
	
	function _moveChildren($fromID=-1) {
		$parentIDs=$this->parentIDs;
		foreach ($this->newMenu as $id => $item) {
			if ($id>$fromID and isset($item['self_parent']) and $item['self_parent']>0 and isset($parentIDs[$item['self_parent']])) {
				$this->newMenu[$id]['self_parent']=$parentIDs[$item['self_parent']];
			}
		}
	}
	
	function _addBranch($topItem,$branch) {
		$fromId=$this->lastId;
		$parentIDs=array();
		foreach ($branch as $oldId => $item) {
			if ($item['self_parent']==0) $item['self_parent']=$topItem['id'];
			$newItem=$this->_insertItem($item);
		}
		$this->_moveChildren($fromId);
	}
	
	
	
	
	
}

?>