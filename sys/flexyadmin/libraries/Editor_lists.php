<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Creates js lists for use in html editor
 *
 * @author Jan den Besten
 * @internal
 */
 
class Editor_lists {

	var $type;
  var $CI;

	function __construct($type="img") {
		$this->set_type($type);
    $this->CI=& get_instance();
    $this->CI->load->model('mediatable');
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
			$data['-- INFO -----------------']=NULL;
			$tblSite = $this->CI->data->table('tbl_site')->get_row();
			if (isset($tblSite['url_url'])) {
				$name=str_replace('http://','',$tblSite['url_url']);
				$data[$name]=array('name'=>$name,'url'=>$tblSite['url_url']);
			}
			if (isset($tblSite['email_email'])) {
				$name=$tblSite['email_email'];
				$data['info_'.'link_'.$name]=array('name'=>$name,'url'=>'mailto:'.$tblSite['email_email']);
			}

			// add if asked for, internal links from menu table
			if ($this->CI->cfg->get('CFG_configurations','b_add_internal_links')) {
				$menuTable=get_menu_table();
				if ($this->CI->db->table_exists($menuTable)) {
					$data['-- SITE LINKS -----------']=NULL;
          $this->CI->data->table( $menuTable );
					$menuFields = $this->CI->data->get_setting('fields');
					$titleField = current(filter_by( $menuFields, 'str'));
					$menuFields = array_combine($menuFields,$menuFields);
					if (isset($menuFields['uri'])) {
						$this->CI->data->select('id,uri');
						if (isset($menuFields['order'])) {
							$this->CI->data->select('order');
							if (in_array('self_parent',$menuFields)) {
								$this->CI->data->select('self_parent');
								$this->CI->data->path('uri')->path($titleField);
							}
						}
						$this->CI->data->select( $titleField );
						$results = $this->CI->data->get_result();
						// strace_($results);
						// add results to link list
						foreach ($results as $key => $row) {
							$url=$row["uri"];
							$name=$url;
							$name=addslashes($row[$titleField]);
							$data['site_'.$name]=array("url"=>site_url($url),"name"=>$name);
						}
					}
				}
			}
			
			// add links from links table
			$table='tbl_links';
			if ($this->CI->db->table_exists($table)) {
				$data['-- LINKS ----------------']=NULL;
				$titleField='str_title';
	 			$this->CI->db->select($titleField.",url_url");
				$this->CI->db->order_by($titleField);
				$query=$this->CI->db->get($table);
				foreach($query->result_array() as $row) {
					$name=addslashes($row[$titleField]);
					$data['link_'.$name]=array("url"=>$row["url_url"],"name"=>$name);
				}
				$query->free_result();
			}
			
		}
		
		// Media files (for download links)
		$mediaTbl=$this->CI->config->item('CFG_table_prefix')."_".$this->CI->config->item('CFG_media_info');
		if ($this->CI->db->table_exists($mediaTbl)) {
			$this->CI->data->table($mediaTbl);
			$this->CI->data->select('path');
			if (!empty($boolField))	$this->CI->data->where($boolField,1);
			$downloadPaths = $this->CI->data->get_result();
			foreach($downloadPaths as $downloadPath) {
				$files=array();
				$path=$downloadPath["path"];
				$map=$this->CI->config->item('ASSETS').$path;
        $files=$this->CI->assets->get_files($path);
				$data['-- '.strtoupper($this->CI->ui->get($path)).' ----------']=NULL;
				$data=$data + $files;
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
						$list.='["'.$link['type'].': '.$link["name"].'","'.site_url('file/download/'.str_replace(SITEPATH.'assets','',$link["path"])).'/'.$link['name'].'"],';
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
			foreach($data as $name=>$file) {
        if (!empty($file)) {
          $name=$file['name'];
          $filename=$this->CI->config->item('ASSETS').$file['path'].'/'.$name;
          $name=$this->CI->assets->get_img_title($file['path'],$name);
  				if (!empty($name)) {
  					$list.='["'.str_replace($this->CI->config->item('ASSETS'),'',$filename).'","'.$filename.'"],';
  				}
        }
			}
		}
		if (substr($list,strlen($list)-1)==',') $list=substr($list,0,strlen($list)-1);
		$list.=");";
		$ListFile = $this->CI->config->item('PUBLICASSETS')."lists/$jsFile.js";
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
