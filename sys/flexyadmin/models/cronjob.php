<?php

/**
 * Verzorgt de afhandeling van cronjobs
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 */

class Cronjob extends CI_Model {
	
	public function __construct()	{
		parent::__construct();
    $lang=$this->config->item('language');
		$lang=$lang."_".strtoupper($lang);
		setlocale(LC_ALL, $lang);
	}

	public function go()	{
    if ($this->db->table_exists('log_cronjobs')) {
      $this->jobs=$this->config->item('cronjobs');
      foreach ($this->jobs as $key=>$job) {
        $this->jobs[$key] = $this->needs_run($job);
        if ($this->jobs[$key]['needs_run']) {
          $this->jobs[$key]=$this->run($this->jobs[$key]);
        }
      }
    }
	}
  
  
  private function get_last_run($job) {
    $job['last']=$this->db->get_field_where('log_cronjobs','tme_last_run','str_job',$job['name']);
    return $job;
  }
  
  private function needs_run($job) {
    if (!isset($job['last'])) $job=$this->get_last_run($job);
    $last=mysql_to_unix($job['last']);        // unix stamp
    $next=$this->_calc_next($job['every'],$last);
    $job['needs_run'] = ($last < $next) && (time() >= $next);
    trace_(['time'=>unix_to_mysql(time()),'last'=>unix_to_mysql($last),'next'=>unix_to_mysql($next),'run'=>$job['needs_run'] ]);
    return $job;
  }
  
  private function run($job) {
    $name=$job['name'];
    // load
    $this->load->library($name);
    // run
    $job['result']=$this->$name->index($job);
    $job['at']=unix_to_mysql(time());
    // store in db
    if ($job['result']===TRUE) {
      $this->db->set(array('str_job'=>$job['name'],'tme_last_run'=>$job['at']));
      if (empty($job['last']))
        $this->db->insert('log_cronjobs');
      else
        $this->db->where('str_job',$job['name'])->update('log_cronjobs');
    }
    // log
    if (is_string($job['result']))
      log_message('error', 'FlexyAdmin CRONJOB '.$job['name'].' ERROR: '.$job['result']);
    else
      log_message('info', 'FlexyAdmin CRONJOB '.$job['name']);
    return $job;
  }
  
  
  /**
   * Berekent het tijdstip waarop het volgende moment valt 
   * 
   * - 5                  // every 5 minutes
   * - 'day 10:15'        // every day at 10:15
   * - 'week 5 12:00      // every week at 12:00 on friday (day5)
   * - 'month 1 18:00     // every first day of the month at 18:00
   *
   * @param string $every
   * @param int $last unixstamp of last run
   * @return int $next unixstamp of next run
   * @author Jan den Besten
   */
  public function _calc_next($every,$last) {
    $next=false;
    if (is_integer($every) or (is_numeric($every))) {
      $next = $last + (int) $every*60;
    }
    else {
      $every=explode(' ',$every);
      if ($every[0]=='day')
        $time=explode(':',$every[1]);
      else
        $time=explode(':',$every[2]);
      $hour=(int) $time[0];
      $min=(int) $time[1];
      
      $day_of_week=0;
      $day_of_month=0;
      $day_of_year=0;
      
      switch ($every[0]) {
        
        case 'day':
          $start=mktime( 0,0,0 );
          break;
          
        case 'week':
          $a_data=getdate();
          $wday=$a_data['wday']; // 0 = sunday
          $start=mktime( 0,0,0, date('n',$last), date('j')-$wday );
          $day_of_week=(int) $every[1];
          $hour; // Hack, don't know why...
          break;

        case 'month':
          $a_data=getdate();
          $mday=$a_data['mday']-1; // 1 = 1e dag
          $start=mktime( 0,0,0, date('n',$last), date('j')-$mday );
          $day_of_month=(int) $every[1] -1;
          break;
      }
      
      $next = $start + $day_of_year*86400 + $day_of_month*86400 + $day_of_week*86400 + $hour*3600 + $min*60;
      
      // trace_([$every,'last'=>unix_to_mysql($last),'start'=>unix_to_mysql($start),'next'=>unix_to_mysql($next)]);
    }
    return $next;
  }
  
  

}




?>