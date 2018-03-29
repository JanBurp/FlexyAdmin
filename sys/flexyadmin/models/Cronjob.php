<?php

/** \ingroup models
 * Verzorgt de afhandeling van cronjobs
 *
 * @author Jan den Besten
 */

class Cronjob extends CI_Model {

	public function __construct()  {
    parent::__construct();
    $this->load->model('log_activity');
    $lang=$this->config->item('language');
    $lang=$lang."_".strtoupper($lang);
    setlocale(LC_ALL, $lang);
    ini_set('max_execution_time', 600); // 10 minuten mag het script erover doen.
  }


  private function get_jobs_info() {
    $jobs = $this->config->item('cronjobs');
    foreach ($jobs as $key=>$job) {
      if (substr($job['every'],0,5)=='model') {
        $model_job = $this->_every_model($job['every'],$key);
        $job = array_merge($job,$model_job);
      }
      $jobs[$key] = $job;
    }
    return $jobs;
  }


	public function go() {
    if ($this->data->table_exists('log_cronjobs')) {
      
      $this->jobs = $this->get_jobs_info();
      
      // Eerst alle informatie verzamelen
      foreach ($this->jobs as $key=>$job) {
        
        // Laatste run
        $last = $this->data->table('log_cronjobs')->where('str_job',$job['name'])->get_field('tme_last_run');
        if ($last)
          $job['last'] = mysql_to_unix($last);
        else
          $job['last'] = 0;

        // Volgende run
        $job['next'] = $this->_calc_next( $job['every'], $job['last'] );

        // Moet de job gebeuren?
        $job['run'] = $this->job_needs_run($job);
        
        $this->jobs[$key] = $job;
      }
 
      // Run de jobs die moeten runnen
      foreach ($this->jobs as $key => $job) {

        if ($this->jobs[$key]['run']) {
          
          // log in db
          $job['at'] = time();
          $this->log_last_run($job['name'],$job['at']);

          // Run
          $name = ucfirst($job['name']);
          $this->load->model($name);
          $job['result'] = $this->$name->index($job);

          // log
          if (is_string($job['result']))
            log_message('error', 'FlexyAdmin CRONJOB '.$job['name'].' ERROR: '.$job['result']);
          else
            log_message('info', 'FlexyAdmin CRONJOB '.$job['name']);
        }
        else {
          log_message('info', 'FlexyAdmin CRONJOB '.$job['name'].' dit not run');
        }
        
        $this->jobs[$key] = array_merge($this->jobs[$key],$job);
      }
      
      
      // echo results
      foreach ($this->jobs as $job) {
        $job = $this->human_job($job);

        // Log informatie
        trace_($job);
        $this->log_activity->add( 'cronjob', array2json($job,false), $this->jobs[$key]['name'] );
      }
      

    }
  }


  private function _every_model($every,$name) {
    $model = remove_prefix($every,' ');
    $this->load->model($model,'model');
    return $this->model->cronjob($name);
  }


  
  /**
   * Logged cronjob
   */
  public function log_last_run($name,$last) {
    $job_exists = $this->data->table('log_cronjobs')->where('str_job',$name)->get_row();
    // if ($this->daylight_saving) $last=$last-TIME_HOUR;
    $this->data->set( array( 'str_job'=>$name, 'tme_last_run'=>unix_to_mysql($last)) );
    if ( !$job_exists )
      $this->data->insert();
    else
      $this->data->where('str_job',$name)->update();
  }

  public function human_job($job) {
    $job['nu'] = unix_to_normal(time());
    $job['last_human'] = unix_to_normal($job['last']);
    $job['next_human'] = unix_to_normal($job['next']);
    if (isset($job['at'])) {
      $job['at_human'] = unix_to_normal($job['at']);
    }
    return $job;
  }


  /**
   * Zorgt ervoor dat de startpositie is ingesteld. Als je bv een cronjob (dynamisch) aanzet.
   */
  public function log_start_setting() {
    $cronjobs = $this->get_jobs_info();
    foreach ($cronjobs as $name => $job) {
      $last  = $this->cronjob->_calc_next( $job['every'], time() );
      if (isset($job['interval'])) $last = $last - $job['interval'];
      $this->cronjob->log_last_run($name,$last);
    }
  }
  
  
   /**
   * Berekent het tijdstip waarop het volgende moment valt, met dit als input
   * 
   * - 5                  // iedere 5 minuten (marge van 59 seconden)
   * - 'day 10:15'        // iedere dag om 10:15
   * - 'week 0 12:00      // iedere week om 12:00 op vrijdag (dag 0)
   * - 'month 1 18:00     // iedere eerste dag van de maand om 18:00
   *
   * @param string $every (zie voorbeelden hierboven)
   * @param int $last unixstamp of last run
   * @return $next
   * @author Jan den Besten
   */
  public function _calc_next( $every, $last ) {
    $next=false;
    
    // Per minuut, laatste is afronden op laatste minuut
    if (is_integer($every) or (is_numeric($every))) {
      $start = floor($last/60) * 60;
      $next  = (int) $start + (int) $every * TIME_MINUTE;
      // trace_(['every'=>$every,'last'=>unix_to_normal($last),'start'=>unix_to_normal($start),'next'=>unix_to_normal($next)]);
    }
    
    // Andere keuze, splits $every in soort, dagen, uren en minuten
    else {
      $every = explode(' ',$every);
      $type  = $every[0];
      $day   = 0;
      $time  = explode(':',$every[1]);
      if ( $type!=='day' ) {
        $day  = (int)$every[1];
        $time = explode(':',$every[2]);
      }
      $hour = (int) $time[0];
      $min  = (int) $time[1];

      switch ($type) {
        
        // per dag: startmoment voor next is laatste run dag om 00:00, next is dan de uren en minuten erbij op deze dag
        case 'day':
          $start = mktime( 0,0,0, date('n',$last), date('j',$last), date('Y',$last) );
          $next  = (int) $start + $hour*TIME_HOUR + $min*TIME_MINUTE;
          // Next (met marge van 61 minuten) kan nooit kleiner zijn dan laatste keer, tel er dan een dag bij op
          $marge = TIME_HOUR;
          if (($next-$marge)<$last) $next+=TIME_DAY;
          // Compenseer overgang van zomer/winter tijd
          $daylight_saving_difference = date('I',$last) - date('I',$next);
          $next += $daylight_saving_difference * TIME_HOUR;
          break;
        
        // per week: - start is zondag van laatste runweek om 0:00, - next is start + de dag vd week en het tijdstip
        case 'week':
          $day_of_week = date('w',$last); // 0..7
          $start = mktime( 0,0,0, date('n',$last), date('j',$last), date('Y',$last) ) - ($day_of_week * TIME_DAY);
          $next  = (int) $start + ($day * TIME_DAY) + $hour*TIME_HOUR + $min*TIME_MINUTE;
          // Next kan nooit kleiner zijn dan laatste keer, en marge van 6 dagen, tel er dan een week bij op
          $marge = 6 * TIME_DAY;
          if (($next-$marge)<$last) $next+=TIME_WEEK;
          // Compenseer overgang van zomer/winter tijd
          $daylight_saving_difference = date('I',$last) - date('I',$next);
          $next += $daylight_saving_difference * TIME_HOUR;
          break;

        // per maand: startmoment is 1e dag van de volgende maand om 0:00, next wordt dan start + de dag en tijd van de maand
        case 'month':
          $days_in_month = date('t',$last); // 28..31
          $day_of_month  = date('j',$last); // 1..31
          $start = mktime( 0,0,0, date('n',$last), date('j',$last), date('Y',$last) ) + (-$day_of_month+$days_in_month) * TIME_DAY;
          $next  = (int) $start + $day * TIME_DAY + $hour*TIME_HOUR + $min*TIME_MINUTE;
          // Next kan nooit kleiner zijn dan laatste keer, tel er dan een maand bij op, marge een drie weken
          $marge = 3* TIME_WEEK;
          if (($next-$marge)<$last) $next+=($days_in_month * TIME_DAY);
          // Compenseer overgang van zomer/winter tijd
          $daylight_saving_difference = date('I',$last) - date('I',$next);
          $next += $daylight_saving_difference * TIME_HOUR;
          break;
      }
      
    }
    return $next;
  }

  public function job_needs_run($job) {
    $run = false;
    if ($job['next']) $run = ( time()>=$job['next'] );
    return $run;
  }
  
  

}




?>
