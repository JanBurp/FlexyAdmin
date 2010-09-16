<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class plugin_stats extends plugin_ {

	var $logTable;
	var $Data;
	var $xmlData;
	var $Total;
	var $Year;
	var $Month;
	var $MonthTxt;
	var $Time;
	var $url;
	
	function init($init=array()) {
		parent::init($init);
		$this->CI->load->helper('date');
	}
	
	function _admin_api($args=NULL) {
		if (isset($args[0])) $year=$args[0];
		if (isset($args[1])) $month=$args[1];

		$this->CI->_add_content(h($this->plugin,1));

		$this->CI->load->model("grid");
		$this->CI->load->model("graph");
		$this->CI->lang->load("stats");
		
		$statTypes=array(	'total',
											'this_year',
											'this_month',
											'top_10_pages',
											'top_10_referers',
											'top_10_google',
											'top_10_browsers',
											'top_10_platform' );

		if (empty($year))  $year=date('Y');
		if (empty($month)) $month=date('m');
		
		$todayMonth=date('n');
		$todayYear=date('Y');
		
		$this->Data=array();
		$this->Data['year']=$year;
		$this->Data['month']=$month;
		$this->Year=$year;
		$this->Time=mktime(0,0,0,$month,1,$year);
		$this->Month=date('m',$this->Time);
		$this->MonthTxt=strftime('%B',$this->Time);

		// Is there xml data for earlier years? Give option to show it
		$xmlYearFiles=read_map('site/stats','xml');
		$years='';
		foreach ($xmlYearFiles as $file => $info) {
			if (strlen($file)>8)
				unset($xmlYearFiles[$file]);
			else {
				$y=substr($file,0,4);
				$years=add_string($years,anchor(site_url('admin/plugin/stats/'.$y),$y),'|');
			}
		}
		if (!empty($years)) $this->CI->_add_content('<p>'.$years.'</p>');


		
		$this->url=$this->CI->db->get_field('tbl_site','url_url');
		$this->logTable=$this->CI->config->item('LOG_table_prefix')."_".$this->CI->config->item('LOG_stats');

		// get data from XML (if exists)
		$this->Data=$this->_stat_data_from_xml();

		// Get data from DB if it doesn't exists yet.
		foreach ($statTypes as $type) {
			if (
					($type!='this_year') and (!isset($this->Data[$type]) or $month==$todayMonth)
					or
					($type=='this_year' and !isset($this->Data['this_year']) )
				)
				$this->Data[$type]=$this->_stat_data_from_db($type);
		}
		if (!is_array($this->Data['this_year'])) $this->Data['this_year']=array();
		// Add current month to this_year from DB
		$thisYearMonth=$this->_stat_data_from_db('this_year');
		if (isset($thisYearMonth[$todayMonth])) {
			$this->Data['this_year'][$todayMonth]=$thisYearMonth[$todayMonth];
		}

		
		// show data
		foreach ($statTypes as $type) {
			$this->_show_stat($type);
		}

		// save data as XML
		$this->_stat2xml();

		// Check if there is older data than this month (in DB and XML) create XML data for it
		$oldMonth=$month;
		$oldYear=$year;
		$notReady=TRUE;
		do {
			$oldMonth--;
			if ($oldMonth<1) { $oldMonth=12; $oldYear--; }
			$xmlFile=$this->_xmlMonthFile($oldMonth,$oldYear);
			if (!file_exists($xmlFile)) {
				$oldStats=array();
				$oldStats['month']=$oldMonth;
				$oldStats['year']=$oldYear;
				foreach ($statTypes as $type) {
					$oldStats[$type]=$this->_stat_data_from_db($type,$oldMonth,$oldYear);
				}
				if (empty($oldStats[$type])) $notReady=FALSE;
				else $this->_stat2xml($oldStats);
			}
		} while ($notReady);
		
		// Delete data from DB older than current month
		$this->CI->db->where('tme_date_time <',date('Y-m'));
		$this->CI->db->delete($this->logTable);
		
		$this->CI->_show_type("stats");
	}

	function _lang($stats) {
		if (is_array($stats)) {
			foreach ($stats as $id => $row) {
				foreach ($row as $key => $value) {
					$stats[$id][lang('stats_'.$key)]=$value;
					unset($stats[$id][$key]);
				}
			}
		}
		return $stats;
	}

	function _add_graph($stats,$title,$s='') {
		if (!empty($stats)) {
			$graph=new graph();
			$graph->set_data($stats,langp('stats_'.$title,$s));
			$graph->set_max(find_max($stats,'views'));
			$renderGraph=$graph->render("html","","grid graph stats");
			$data=$this->CI->load->view("admin/graph",$renderGraph,true);
		}
		else $data="";
		$this->CI->_add_content($data);
		$this->CI->_add_content(br());
	}
	
	function _add_table($stats,$title,$s='') {
		if (!empty($stats) and is_array($stats)) {
			$stats=$this->_lang($stats);
			$grid=new grid();
			$grid->set_data($stats,langp('stats_'.$title,$s));
			$renderGrid=$grid->render("html","","grid home");
			$data["stats"]=$this->CI->load->view("admin/grid",$renderGrid,true);
		}
		else $data["stats"]="";
		$this->CI->_add_content($data["stats"]);		
		$this->CI->_add_content(br());
	}


	function _show_stat($type) {
		$data=$this->Data[$type];
		switch ($type) {
			case 'this_year':
				foreach ($data as $key => $value) {
					$data[$key]['month']=anchor(site_url('admin/plugin/stats/'.$this->Year.'/'.$value['month']),strftime('%b',mktime(0,0,0,$value['month'])));
				}
				$this->_add_graph($data,$type,$this->Year);
				break;
			case 'this_month':
				foreach ($data as $key => $value) {
					$dayNr=date('w',mktime(0,0,0,$this->Data['month'],$value['day'],$this->Data['year']));
					if ($dayNr==0 or $dayNr==6) $data[$key]['day']=span('weekend').$data[$key]['day']._span();
				}
				$this->_add_graph($data,$type,$this->MonthTxt);
				break;

			case 'top_10_referers':
			case 'top_10_pages':
				if ($type=='top_10_referers') {
					foreach ($data as $key => $value) {
						$data[$key]['referer']=anchor($value['referer'],str_replace('http://','',$value['referer']),array('target'=>'_blank'));
					}
				}
				else {
					foreach ($data as $key => $value) {
						$data[$key]['page']=anchor($value['page'],$value['page'],array('target'=>'_blank'));
					}
				}
			case 'top_10_google':
			case 'top_10_browsers':
			case 'top_10_platform':
				$this->_add_table($data,$type,$this->MonthTxt);
				break;
		}
		
	}

	function _xmlYearFile($year='') {
		if (empty($year)) $year=$this->Data['year'];
		return $this->CI->config->item('STATS').$year.'.xml';
	}

	function _xmlMonthFile($month='',$year='') {
		if (empty($year)) 	$year=$this->Data['year'];
		if (empty($month)) 	$month=$this->Data['month'];
		return $this->CI->config->item('STATS').$year.'-'.sprintf('%02d',$month).'.xml';
	}

	function _stat2xml($stats=NULL) {
		if (empty($stats)) $stats=$this->Data;
		
		$xmlYearFile=$this->_xmlYearFile($stats['year']);
		$xmlYear=array2xml(array('stats'=> array('year'=>$stats['year'],'this_year'=>$stats['this_year'])) );
		write_file($xmlYearFile, $xmlYear);
		
		$xmlMonthFile=$this->_xmlMonthFile($stats['month'],$stats['year']);
		$monthData=$stats;
		unset($monthData['this_year']);
		$xmlMonth=array2xml(array('stats'=>$monthData));
		write_file($xmlMonthFile, $xmlMonth);
	}


	function _stat_data_from_xml() {
		$xmlYearFile=$this->_xmlYearFile();
		if (file_exists($xmlYearFile)) {
			$xmlYear=read_file($xmlYearFile);
			$yearData=xml2array($xmlYear);
		}
		
		$xmlMonthFile=$this->_xmlMonthFile();
		if (file_exists($xmlMonthFile)) {
			$xmlMonth=read_file($xmlMonthFile);
			$monthData=xml2array($xmlMonth);
		}

		$xmlData=$this->Data;
		if (isset($yearData['stats'])) 	$xmlData=array_merge($xmlData,$yearData['stats']);
		if (isset($monthData['stats'])) $xmlData=array_merge($xmlData,$monthData['stats']);
		return $xmlData;
	}

	function _stat_data_from_db($type,$month='',$year='') {
		if (empty($month))	$month=$this->Month;
		if (empty($year))		$year=$this->Year;
		$data=NULL;
		$limit=10;
		switch ($type) {
			case 'total':
				// Total
				$this->CI->db->select("COUNT('id') as total");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$data=$this->CI->db->get_row($this->logTable);
				$data=$data['total'];
				$this->Total=$data;
				break;
			case 'this_year':
				$this->CI->db->select("MONTH(tme_date_time) as `month`, COUNT(DATE(tme_date_time)) as `views`");
				$this->CI->db->where(array('YEAR(tme_date_time) >=' => $year, 'YEAR(tme_date_time) <' => $year+1) );
				$this->CI->db->group_by('`month`');
				$this->CI->db->order_by("`month`");
				$limit=12;
				break;
			case 'this_month':
				$this->CI->db->select("DAYOFMONTH(tme_date_time) as `day`, COUNT(DATE(tme_date_time)) as `views`");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->db->group_by('`day`');
				$this->CI->db->order_by("`day`");
				$limit=0;
				break;
			case 'top_10_pages':
				$this->CI->db->select("str_uri as page, COUNT(`str_uri`) as hits");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->db->group_by("page");
				$this->CI->db->order_by("hits DESC");
				break;
			case 'top_10_referers':
				$this->CI->db->select("str_referrer as referer, COUNT(`str_referrer`) as hits");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->db->where('str_referrer !=','');
				if (!empty($this->url)) {
					$this->url=str_replace('http://','',$this->url);
					$this->url=str_replace('www.','',$this->url);
					$this->CI->db->not_like('str_referrer',$this->url);
				}
				$this->CI->db->group_by('str_referrer');
				$this->CI->db->order_by("hits DESC");
				break;
			case 'top_10_google':
				$this->CI->db->select("str_referrer as search, COUNT(`str_referrer`) as hits");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->db->like('str_referrer','q=');
				$this->CI->db->group_by('str_referrer');
				$this->CI->db->order_by("hits DESC");
				$limit=50;
				break;
			case 'top_10_browsers':
				$this->CI->db->select("str_browser as browser, str_version as version, COUNT(`str_browser`) as hits, (COUNT(`str_browser`)/".$this->Total."*100) as percent");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->db->group_by('str_browser,str_version');
				$this->CI->db->order_by("hits DESC");
				break;
			case 'top_10_platform':
				$this->CI->db->select("str_platform as platform, COUNT(`str_platform`) as hits, (COUNT(`str_platform`)/".$this->Total."*100) as percent");
				$this->CI->db->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->db->group_by('str_platform');
				$this->CI->db->order_by("hits DESC");
				break;
		}
		
		// get data
		if ($type!='total')
			$data=$this->CI->db->get_results($this->logTable,$limit);
		
		// special data transformations
		switch ($type) {
			case 'this_year':
				$newData=array();
				foreach ($data as $key => $value)
					$newData[$value['month']]=$value;
					$data=$newData;
				break;
			case 'this_month':
				$newData=array();
				foreach ($data as $key => $value)
					$newData[$value['day']]=$value;
					$data=$newData;
				break;
			case 'top_10_google':
				$gstats=array();
				foreach ($data as $key => $value) {
					$term=strtolower($value['search'].'&');
					preg_match("/q=(.*?)&/",$term,$matches);
					if (isset($matches[1])) {
						$term=urldecode($matches[1]);
						$stats[$key]['search']=$term;
						if (array_key_exists($term,$gstats))
							$gstats[$term]['hits']++;
						else
							$gstats[$term]=array('search'=>$term,'hits'=>$value['hits']);
					}
				}
				$gstats=sort_by($gstats,'hits',TRUE);
				$gstats=array_slice($gstats,0,10);
				$data=array();
				foreach ($gstats as $value) {
					$data[]=$value;
				}
				break;
			case 'top_10_browsers':
			case 'top_10_platform':
				foreach ($data as $key => $value) {
					$data[$key]['percent']=round($value['percent'],1);
				}
				break;
		}
		
		return $data;
	}



	
}

?>