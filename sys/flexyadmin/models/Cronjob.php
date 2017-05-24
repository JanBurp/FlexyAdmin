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

	public function go() {
    if ($this->data->table_exists('log_cronjobs')) {
      
      $this->jobs = $this->config->item('cronjobs');
      
      // Eerst alle informatie verzamelen
      $this->data->table('log_cronjobs');
      foreach ($this->jobs as $key=>$job) {
        $last = $this->data->where('str_job',$job['name'])->get_field('tme_last_run');
        if ($last)
          $job['last'] = mysql_to_unix($last);
        else
          $job['last'] = 0;
        $job['next'] = $this->_calc_next( $job['every'], $job['last'] );
        // Moet de job gebeuren?
        $job['run']  = ( time()>=$job['next'] );
        // $job['run']  = true;
        $this->jobs[$key] = $job;
      }
      
      
      // Run de jobs die moeten runnen
      foreach ($this->jobs as $key => $job) {

        if ($this->jobs[$key]['run']) {
          
          // log in db
          $job['at'] = time();
          // Nieuw?
          $job_exists = $this->data->table('log_cronjobs')->where('str_job',$job['name'])->get_row();
          // Log
          $this->data->set( array( 'str_job'=>$job['name'], 'tme_last_run'=>unix_to_mysql($job['at'])) );
          if ( !$job_exists )
            $this->data->insert();
          else
            $this->data->where('str_job',$job['name'])->update();

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
        $job['nu'] = unix_to_normal(time());
        $job['last'] = unix_to_normal($job['last']);
        $job['next'] = unix_to_normal($job['next']);
        if (isset($job['at'])) {
          $job['at'] = unix_to_normal($job['at']);
        }

        // Log informatie
        trace_($job);
        $this->log_activity->add( 'cronjob', array2json($job,false), $this->jobs[$key]['name'] );
      }
      

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

      // if ($daylight_saving) $hour -= TIME_HOUR;

      switch ($type) {
        
        // per dag: startmoment voor next is laatste run dag om 00:00, next is dan de uren en minuten erbij op deze dag
        case 'day':
          $start = mktime( 0,0,0, date('n',$last), date('j',$last), date('Y',$last) );
          $next  = (int) $start + $hour*TIME_HOUR + $min*TIME_MINUTE;
          // Next kan nooit kleiner zijn dan laatste keer, tel er dan een dag bij op
          if ($next<$last) $next+=TIME_DAY;
          break;
        
        // per week: - start is zondag van laatste runweek om 0:00, - next is start + de dag vd week en het tijdstip
        case 'week':
          $day_of_week = date('w',$last); // 0..7
          $start = mktime( 0,0,0, date('n',$last), date('j',$last), date('Y',$last) ) - ($day_of_week * TIME_DAY);
          $next  = (int) $start + ($day * TIME_DAY) + $hour*TIME_HOUR + $min*TIME_MINUTE;
          // Next kan nooit kleiner zijn dan laatste keer, tel er dan een week bij op
          if ($next<$last) $next+=TIME_WEEK;
          break;

        // per maand: startmoment is 1e dag van de volgende maand om 0:00, next wordt dan start + de dag en tijd van de maand
        case 'month':
          $days_in_month = date('t',$last); // 28..31
          $day_of_month  = date('j',$last); // 1..31
          $start = mktime( 0,0,0, date('n',$last), date('j',$last), date('Y',$last) ) + (-$day_of_month+$days_in_month) * TIME_DAY;
          $next  = (int) $start + $day * TIME_DAY + $hour*TIME_HOUR + $min*TIME_MINUTE;
          // Next kan nooit kleiner zijn dan laatste keer, tel er dan een maand bij op
          if ($next<$last) $next+=($days_in_month * TIME_DAY);
          break;
      }
      
    }
    return $next;
  }
  
  

}




?>
