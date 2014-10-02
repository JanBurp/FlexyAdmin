<?php

class Cronjob extends CI_Model {
	
	public function __construct()	{
		parent::__construct();
	}

	public function go()	{
    if ($this->db->table_exists('cfg_cronjobs')) {
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
    $job['last']=$this->db->get_field_where('cfg_cronjobs','tme_last_run','str_job',$job['name']);
    return $job;
  }
  
  private function needs_run($job) {
    if (!isset($job['last'])) $job=$this->get_last_run($job);
    $last=mysql_to_unix($job['last']);    // unix stamp
    $every=$job['every']*60;              // from minutes to secs
    $job['needs_run'] = ($last + $every) < time();
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
        $this->db->insert('cfg_cronjobs');
      else
        $this->db->where('str_job',$job['name'])->update('cfg_cronjobs');
    }
    // log
    if (is_string($job['result']))
      log_message('error', 'FlexyAdmin CRONJOB '.$job['name'].' ERROR: '.$job['result']);
    else
      log_message('info', 'FlexyAdmin CRONJOB '.$job['name']);
    return $job;
  }
  

}




?>