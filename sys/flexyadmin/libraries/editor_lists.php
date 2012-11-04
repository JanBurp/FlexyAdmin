<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Creates js lists for use in html editor
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */
 
class Editor_lists {

	var $type;
  var $CI;

	function __construct($type="img") {
		$this->set_type($type);
    $this->CI=& get_instance();
	}

	function set_type($type="img") {
		$types=array();
		$types["img"]				=array(	"boolean_field" => "b_in_img_list",
																"array_name"		=> "tinyMCEImageList",
																"file_name"			=> "img_list");
		$types["media"]			=array(	"boolean_field" => "b_in_media_list",
																"array_name"		=> "tinyMCEMediaList",
																"file_name"			=> "media_list");
		$types["downloads"]	=array(	"boolean_field" => "b_in_link_list",
																"array_name"		=> "tinyMCELinkList",
																"file_name"			=> "link_list");
		$types["links"]			=array(	"array_name"		=> "tinyMCELinkList",
																"file_name"			=> "link_list");
		$types["embed"]			=array(	"array_name"		=> "tinyMCEEmbedList",
																"file_name"			=> "embed_list");
		$this->type=$types[$type];
	}

	function create_list($type="") {
		if ($type=="links") $type="downloads";
		if (!empty($type)) $this->set_type($type);
		$t=$this->type;
		$boolField=el("boolean_field",$t);
		$jsArray	=$t["array_name"];
		$jsFile		=$t["file_name"];

		$data=array();
		
		if ($type=="downloads") {
			
			// add special links from tbl_site
			$data['-- info -----------------']=NULL;
			$tblSite=$this->CI->db->get_row('tbl_site');
			if (isset($tblSite['url_url'])) {
				$name=str_replace('http://','',$tblSite['url_url']);
				$data[$name]=array('name'=>$name,'url'=>$tblSite['url_url']);
			}
			if (isset($tblSite['email_email'])) {
				$name=$tblSite['email_email'];
				$data[$name]=array('name'=>$name,'url'=>'mailto:'.$tblSite['email_email']);
			}

			// add if asked for, internal links from menu table
			if ($this->CI->cfg->get('CFG_configurations','b_add_internal_links')) {
				$menuTable=get_menu_table();
				if ($this->CI->db->table_exists($menuTable)) {
					$data['-- site links -----------']=NULL;
					$titleField=$this->CI->db->get_first_field($menuTable,'str');
					$menuFields=$this->CI->db->list_fields($menuTable);
					$menuFields=array_combine($menuFields,$menuFields);
					if (isset($menuFields['uri'])) {
						$this->CI->db->select('id,uri');
						if (isset($menuFields['order'])) {
							$this->CI->db->select('order');
							if (in_array('self_parent',$menuFields)) {
								$this->CI->db->select('self_parent');
								$this->CI->db->order_as_tree();
								$this->CI->db->uri_as_full_uri(TRUE,$titleField);
							}
						}
						$this->CI->db->select_first('str');
						$results=$this->CI->db->get_results($menuTable);
						// strace_($results);
						// add results to link list
						$nameField=$this->CI->db->get_select_first(0);
						foreach ($results as $key => $row) {
							$url=$row["uri"];
							$name=$url;
							$name=addslashes($row[$nameField]);
							$data[$name]=array("url"=>site_url($url),"name"=>$name);
						}
					}
				}
			}
			
			// add links from links table
			$table=$this->CI->cfg->get('CFG_configurations','table');
			if ($this->CI->db->table_exists($table)) {
				$data['-- links ----------------']=NULL;
				$titleField=$this->CI->db->get_first_field($table,'str');
				if ($this->CI->db->table_exists($table)) {
		 			$this->CI->db->select($titleField.",url_url");
					$this->CI->db->order_by($titleField);
					$query=$this->CI->db->get($table);
					foreach($query->result_array() as $row) {
						$name=addslashes($row[$titleField]);
						$data[$name]=array("url"=>$row["url_url"],"name"=>$name);
					}
					$query->free_result();
				}
			}
			
		}
		
		if ($type=='embed') {
			$embedTbl='tbl_embeds';
			if ($this->CI->db->table_exists($embedTbl)) {
				$titleField=$this->CI->db->get_first_field($embedTbl,'str');
				$embeds=$this->CI->db->get_result($embedTbl);
				foreach($embeds as $row) {
					$data[$row[$titleField]]=array("embed"=>$row["stx_embed"],"name"=>$row[$titleField]);
				}
			}
		}
		else {
			// Media files (for download links)
			$mediaTbl=$this->CI->config->item('CFG_table_prefix')."_".$this->CI->config->item('CFG_media_info');
			if ($this->CI->db->table_exists($mediaTbl)) {
				$this->CI->db->select('path');
				if (!empty($boolField))	$this->CI->db->where($boolField,1);
				$downloadPaths=$this->CI->db->get_result($mediaTbl);
				foreach($downloadPaths as $downloadPath) {
					$files=array();
					$path=$downloadPath["path"];
					$map=$this->CI->config->item('ASSETS').$path;
					$files=read_map($map);
					$files=not_filter_by($files,"_");
					ignorecase_ksort($files);
					$data['-- '.$this->CI->ui->get($path).' ----------']=NULL;
					$data=$data + $files;
				}
			}
		}

		// ignorecase_ksort($data);
		
		// trace_($type);
		// trace_($data);

		// set list
		$list="var $jsArray = new Array(";
		$first=true;
		if ($type=="links" or $type=="downloads") {
			foreach($data as $name=>$link) {
				if (empty($link)) {
					if (!$first) $list.='[""],';
					$list.='["'.$name.'"],';
					$first=false;
				}
				else {
					if ($type=="downloads" and isset($link['type']))
						$list.='["'.$link['type'].': '.$link["name"].'","'.site_url('file'.str_replace(SITEPATH.'assets','',$link["path"])).'"],';
					else
						$list.='["'.$link["name"].'","'.$link["url"].'"],';
				}
			}
			$list.='[""],';
		}
		elseif ($type=='embed') {
			foreach($data as $name=>$embed) {
				$list.='["'.$name.'",\''.$embed['embed'].'\'],';
			}
		}
		else {
      // trace_($data);
			foreach($data as $name=>$file) {
        if (!empty($file)) {
          $name=$this->CI->mediatable->get_img_title($file['path']);
          // $name=nice_string(get_file_without_extension($file["name"]));
          // trace_($name);
  				if (!empty($name)) {
  					$list.='["'.$name.'","'.$file["path"].'"],';
  				}
        }
			}
		}
		if (substr($list,strlen($list)-1)==',') $list=substr($list,0,strlen($list)-1);
		$list.=");";
		$ListFile=SITEPATH."assets/lists/$jsFile.js";
    $list=str_replace('&nbsp;',' ',$list);
		$result=write_file($ListFile, $list);
		return $result;
	}

	function delete($file,$type="") {
		$this->create_list($type);
	}

	function add($file,$type="") {
		$this->create_list($type);
	}

}
?>
