<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Geeft statistieken visueel weer
 *
 * @author Jan den Besten
 * @internal
 */
class Plugin_stats extends Plugin {

	var $logTable;
	var $Data;
	var $xmlData;
	var $Total=0;
	var $Year;
	var $Month;
	var $MonthTxt;
	var $Time;
	var $url;

  private $keep = 93; // days to keep log in database (3 months)


	public function __construct() {
		parent::__construct();
		$this->CI->load->helper('date');
		$this->CI->load->model("grid");
		$this->CI->load->model("graph");
		$this->CI->lang->load("stats");
	}

	
	public function _admin_api($args=NULL) {
		if ( !$this->CI->flexy_auth->allowed_to_use_cms()) return false;
		if (isset($args[0])) $year=$args[0];
		if (isset($args[1])) $month=$args[1];

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
		$xmlYearFiles=read_map(SITEPATH.'stats','xml',FALSE,FALSE);
		ksort($xmlYearFiles);
		
		$years = array();
		foreach ($xmlYearFiles as $file => $info) {
			if (strlen($file)>8)
				unset($xmlYearFiles[$file]);
			else {
				$y=substr($file,0,4);
				$years[] = '<a class="btn btn-sm btn-warning" href="'.(site_url($this->CI->config->item('API_home').'plugin/stats/'.$y)).'">'.$y.'</a>';
			}
		}
    if (count($years)>1) {
      $years = implode(' ',$years);
      $this->add_content('<p>'.$years.'</p>');		  
    }

		$this->url = $this->CI->data->table('tbl_site')->get_field('url_url');
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
    $keep_from = unixdate_add_days(time(),-$this->keep);
		$oldMonth=$month;
		$oldYear=$year;
		$notReady=TRUE;
		do {
			$xmlFile=$this->_xmlMonthFile($oldMonth,$oldYear);
      $xmlTime=mktime(0,0,0,$oldMonth,0,$oldYear);
			if (!file_exists($xmlFile) or ($xmlTime+$keep_from>time())) {
				$oldStats=array();
				$oldStats['month']=$oldMonth;
				$oldStats['year']=$oldYear;
				foreach ($statTypes as $type) {
					$oldStats[$type]=$this->_stat_data_from_db($type,$oldMonth,$oldYear);
				}
				if (empty($oldStats[$type])) $notReady=FALSE;
				else $this->_stat2xml($oldStats);
			}
			$oldMonth--;
			if ($oldMonth<1) { $oldMonth=12; $oldYear--; }
		} while ($notReady);
		
		// Delete data from DB older than current months
    $delete_from_unix_date = time() - ($this->keep * 24 * 60 * 60);
    $delete_from_date = date('Y-m',$delete_from_unix_date);
    $sql = "DELETE FROM `".$this->logTable."` WHERE `tme_date_time` < '".$delete_from_date."' ";
    $query = $this->CI->db->query($sql);
    
    // $this->_download_links($year,$month);
    return $this->content;
	}

	private function _lang($stats) {
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

	private function _add_graph($stats,$title,$s='') {
		if (!empty($stats)) {
			$graph=new graph();
			$graph->set_data($stats,langp('stats_'.$title,$s));
			$graph->set_max(find_max($stats,'views'));
			$renderGraph=$graph->render("html","","grid graph stats");
			$data=$this->CI->load->view("admin/graph",$renderGraph,true);
  		$this->add_content($data);
      // $this->add_content(br());
		}
	}
	
	private function _add_table($stats,$title,$s='') {
		if (!empty($stats) and is_array($stats)) {
			$stats=$this->_lang($stats);
      $grid=new grid();
      $grid->set_data($stats,langp('stats_'.$title,$s));
      $renderGrid=$grid->render("html","","grid home");
      $data["stats"]=$this->CI->load->view("admin/grid",$renderGrid,true);
      $this->add_content($data["stats"]);
      // $this->add_content(br());
		}
	}


	private function _show_stat($type) {
		$data=$this->Data[$type];
		switch ($type) {
			case 'this_year':
				foreach ($data as $key => $value) {
					$data[$key]['month'] = '<a class="btn btn-sm btn-warning" href="'.site_url($this->CI->config->item('API_home').'plugin/stats/'.$this->Year.'/'.$value['month']).'">'.strftime('%b',mktime(0,0,0,$value['month'])).'</a>';
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
                if (!is_string($value['page'])) $value['page']='';
                $data[$key]['page']=anchor($value['page'],$value['page'],array('target'=>'_blank'));
          }
        }
      case 'top_10_google':
      case 'top_10_browsers':
      case 'top_10_platform':
        foreach ($data as $key => $value) {
          if (isset($value['search']) and !is_string($value['search'])) $data[$key]['search']='';
        }
        $this->_add_table($data,$type,$this->MonthTxt);
        break;
		}
		
	}

  // function _download_links($year,$month) {
  //   $this->add_content(h('Download XML',2));
  //   $xmlYear=SITEPATH.'stats/'.$year.'.xml';
  //   $xmlMonth=SITEPATH.'stats/'.$year.'-'.$month.'.xml';
  //   $this->add_content(anchor($xmlYear));
  //   $this->add_content(anchor($xmlMonth));
  // }

	private function _xmlYearFile($year='') {
		if (empty($year)) $year=$this->Data['year'];
		return $this->CI->config->item('STATS').$year.'.xml';
	}

	private function _xmlMonthFile($month='',$year='') {
		if (empty($year)) 	$year=$this->Data['year'];
		if (empty($month)) 	$month=$this->Data['month'];
		return $this->CI->config->item('STATS').$year.'-'.sprintf('%02d',$month).'.xml';
	}

	private function _stat2xml($stats=NULL) {
		if (empty($stats)) $stats=$this->Data;

		$xmlYearFile=$this->_xmlYearFile($stats['year']);
    // trace_($stats);
    $xmlYearArray=array('stats'=> array('year'=>$stats['year'],'this_year'=>$stats['this_year'])) ;
		$xmlYear=array2xml($xmlYearArray);
    // trace_($xmlYearArray);
		write_file($xmlYearFile, $xmlYear);
		
		$xmlMonthFile=$this->_xmlMonthFile($stats['month'],$stats['year']);
		$monthData=$stats;
		unset($monthData['this_year']);
		$xmlMonth=array2xml(array('stats'=>$monthData));
		write_file($xmlMonthFile, $xmlMonth);
	}

  private function _clean_id_bug($a) {
    foreach ($a as $key => $value) {
      if ($key=='id' and count($a)==1) return $this->_clean_id_bug($value);
      if ($key=='id' and !is_array($value)) unset($a['id']);
      if (is_array($value)) $a[$key]=$this->_clean_id_bug($value);
    }
    return $a;
  }

	private function _stat_data_from_xml() {
		$xmlYearFile=$this->_xmlYearFile();
		$yearData=array();
		if (file_exists($xmlYearFile)) {
			$xmlYear=file_get_contents($xmlYearFile);
			$xmlYear=reformMalformedXML($xmlYear);
			$yearData=xml2array($xmlYear);
      // 'id' bug voorkomen
      $yearData=$this->_clean_id_bug($yearData);
			$yearData['stats']['this_year']=reformXmlArrayKey($yearData['stats']['this_year'],'month');
		}
    
		// other months of this year?
		if (isset($yearData['stats']['this_year'])) {
			$yearDataMonths=$yearData['stats']['this_year'];
			$firstMonth=current($yearDataMonths);
			$firstMonth=el('month',$firstMonth,0);
			if ($firstMonth>1) {
				for ($m=1; $m < $firstMonth ; $m++) { 
					$xmlMonthFile=$this->_xmlMonthFile($m,$this->Data['year']);
					if (file_exists($xmlMonthFile)) {
						$xmlMonth=file_get_contents($xmlMonthFile);
						$monthData=xml2array($xmlMonth);
						$monthTotal=$monthData['stats']['total'];
						$yearData['stats']['this_year'][$m]=array('month'=>$m,'views'=>$monthTotal);
					}
				}
				$this_year=$yearData['stats']['this_year'];
				ksort($this_year);
				$yearData['stats']['this_year']=$this_year;
			}
		}
    
		// this month
		$xmlMonthFile=$this->_xmlMonthFile();
		if (file_exists($xmlMonthFile)) {
			$xmlMonth=file_get_contents($xmlMonthFile);
			$xmlMonth=reformMalformedXML($xmlMonth);
			$monthData=xml2array($xmlMonth);
      $monthData=$this->_clean_id_bug($monthData);
			$monthData['stats']['this_month']=reformXmlArrayKey($monthData['stats']['this_month'],'day');
			if (isset($monthData['stats']['top_10_pages']))     $monthData['stats']['top_10_pages']=reformXmlArrayKey($monthData['stats']['top_10_pages'],'page');
      if (isset($monthData['stats']['top_10_referers']))  $monthData['stats']['top_10_referers']=reformXmlArrayKey($monthData['stats']['top_10_referers'],'referer');
			if (isset($monthData['stats']['top_10_google']))    $monthData['stats']['top_10_google']=reformXmlArrayKey($monthData['stats']['top_10_google'],'search');
			if (isset($monthData['stats']['top_10_browsers']))  $monthData['stats']['top_10_browsers']=reformXmlArrayKey($monthData['stats']['top_10_browsers'],'browser');
			if (isset($monthData['stats']['top_10_platform']))  $monthData['stats']['top_10_platform']=reformXmlArrayKey($monthData['stats']['top_10_platform'],'platform');
		}

		$xmlData=$this->Data;
    // trace_($yearData['stats']);
    // trace_($xmlData);
    if (isset($yearData['stats']))  $xmlData=array_merge($xmlData,$yearData['stats']);
		if (isset($monthData['stats'])) $xmlData=array_merge($xmlData,$monthData['stats']);
    
		return $xmlData;
	}

	private function _stat_data_from_db($type,$month='',$year='') {
    // if ($this->Total<=0) return array();
		if (empty($month))	$month=$this->Month;
		if (empty($year))		$year=$this->Year;
		$data=NULL;
		$limit=10;
    $this->CI->data->table( $this->logTable );
		switch ($type) {
			case 'total':
				// Total
				$this->CI->data->select("COUNT('id') as total");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$data=$this->CI->data->get_row();
				$data=$data['total'];
				$this->Total=$data;
				break;
			case 'this_year':
				$this->CI->data->select("MONTH(tme_date_time) as `month`, COUNT(DATE(tme_date_time)) as `views`");
				$this->CI->data->where(array('YEAR(tme_date_time) >=' => $year, 'YEAR(tme_date_time) <' => $year+1) );
        $this->CI->data->group_by( 'MONTH(`tme_date_time`)' );
				$this->CI->data->order_by( "`month`" );
				$limit=12;
				break;
			case 'this_month':
				$this->CI->data->select("DAYOFMONTH(tme_date_time) as `day`, COUNT(DATE(tme_date_time)) as `views`");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->data->group_by('`day`');
				$this->CI->data->order_by("`day`");
				$limit=0;
				break;
			case 'top_10_pages':
				$this->CI->data->select("str_uri as page, COUNT(`str_uri`) as hits");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->data->group_by( "str_uri" );
				$this->CI->data->order_by( "hits DESC" );
				break;
			case 'top_10_referers':
				$this->CI->data->select("str_referrer as referer, COUNT(`str_referrer`) as hits");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->data->where('str_referrer !=','');
				if (!empty($this->url)) {
					$this->url=str_replace('http://','',$this->url);
					$this->url=str_replace('www.','',$this->url);
					$this->CI->data->not_like('str_referrer',$this->url);
				}
				$this->CI->data->group_by('str_referrer');
				$this->CI->data->order_by("hits DESC");
				break;
			case 'top_10_google':
				$this->CI->data->select("str_referrer as search, COUNT(`str_referrer`) as hits");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->data->like('str_referrer','q=');
				$this->CI->data->group_by('str_referrer');
				$this->CI->data->order_by("hits DESC");
				$limit=50;
				break;
			case 'top_10_browsers':
        $tot=$this->Total * 100;
				$this->CI->data->select("str_browser as browser, str_version as version, COUNT(`str_browser`) as hits, (COUNT(`str_browser`)/".$tot.") as percent");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->data->group_by('str_browser,str_version');
				$this->CI->data->order_by("hits DESC");
				break;
			case 'top_10_platform':
        $tot=$this->Total * 100;
				$this->CI->data->select("str_platform as platform, COUNT(`str_platform`) as hits, (COUNT(`str_platform`)/".$tot.") as percent");
				$this->CI->data->where(array('YEAR(tme_date_time)' => $year, 'MONTH(tme_date_time)' => $month));
				$this->CI->data->group_by('str_platform');
				$this->CI->data->order_by("hits DESC");
				break;
		}
		
		// get data
		if ($type!='total') $data = $this->CI->data->get_result( $limit );
    // if ($type=='this_year') {
    //   trace_($type);
    //   trace_($this->CI->data->last_query());
    //   trace_($data);
    //   // die();
    // }
    
    // trace_(array(
    //   'this->Total'=>$this->Total,
    //   'data'=>$data
    // ));
		
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
    
    if (is_array($data)) {
      foreach ($data as $id => $value) unset($data[$id]['id']);
    }
		
		return $data;
	}

}

?>