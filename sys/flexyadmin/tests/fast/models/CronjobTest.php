<?php

require_once(APPPATH.'/tests/CITestCase.php');

class CronjobTest extends CITestCase {

    protected function setUp() :void  {
      $this->CI->load->model('cronjob');
      $this->CI->load->helper('date');
    }

    public function test_log()  { 
      $this->assertEquals( true,true);
    }


    public function test_calc_next_minutes()  { 
      $tests = array(
        // Net gedaan, volgende is dan
        array(
          'test'  => 1,
          'every' => '1',
          'last'  => time(),
          'next'  => mktime(date('H'),date('i'),0) + TIME_MINUTE,
        ),
        array(
          'test'  => 2,
          'every' => '5',
          'last'  => time(),
          'next'  => mktime(date('H'),date('i'),0) + 5 * TIME_MINUTE,
        ),
        array(
          'test'  => 'Heel lang geleden geweest, moet op eerstvolgende moment',
          'every' => '5',
          'last'  => 0,
          'next'  => 0 + 5 * TIME_MINUTE,
        ),
        // Gedaan, zou nu moeten
        array(
          'test'  => 4,
          'every' => '1',
          'last'  => time() - TIME_MINUTE,
          'next'  => mktime(date('H'),date('i'),0),
        ),
        array(
          'test'  => 5,
          'every' => '5',
          'last'  => time() - 5 * TIME_MINUTE,
          'next'  => mktime(date('H'),date('i'),0),
        ),
      );
      foreach ($tests as $test) {
        $result = $this->CI->cronjob->_calc_next( $test['every'], $test['last'] );
        $this->assertEquals( $test['next'], $result, '['.$test['test'].'] '.unix_to_normal($result). ' zou '. unix_to_normal($test['next']).' moeten zijn. ( every=>`'.$test['every'].'`, last=>'.unix_to_normal($test['last']).')' );
      }
    }

    public function test_calc_next_day()  {
      $tests = array(
        // Wanneer is volgende (net geweest)
        array(
          'test'  => 'Precies vandaag geweest, moet morgen',
          'every' => 'day '.date('H:i'),
          'last'  => time(),
          'next'  => mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')) + TIME_DAY,
        ),
        array(
          'test'  => 'lang geleden geweest, moet vandaag dus op eerste moment',
          'every' => 'day '.date('H:i'),
          'last'  => 0,
          'next'  => 0 + (date('G')-1) * TIME_HOUR + (int)date('i') * TIME_MINUTE,
        ),
        array(
          'test'  => 'Vandaag, wat later dan nu, geweest, moet morgen',
          'every' => 'day '.date('H:i'),
          'last'  => time() + rand(TIME_MINUTE,TIME_HOUR),
          'next'  => mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')) + TIME_DAY,
        ),
      );
      foreach ($tests as $test) {
        $result = $this->CI->cronjob->_calc_next( $test['every'], $test['last'] );
        $this->assertEquals( $test['next'], $result, '['.$test['test'].'] '.unix_to_normal($result). ' zou '. unix_to_normal($test['next']).' moeten zijn. ( every=>`'.$test['every'].'`, last=>'.unix_to_normal($test['last']).')' );
      }
    }

    // public function test_calc_next_week()  {
    //   $tests = array(
    //     // Wanneer is volgende (net geweest)
    //     array(
    //       'test'  => 'Precies vandaag geweest, moet morgen',
    //       'every' => 'week '.date('w H:i'),
    //       'last'  => time(),
    //       'next'  => mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')) + TIME_WEEK,
    //     ),
    //     array(
    //       'test'  => 'Heel lang geleden geweest, moet op eerstvolgende moment',
    //       'every' => 'week '.date('w H:i'),
    //       'last'  => 0,
    //       'next'  => 0 + (date('w')+3) * TIME_DAY + (date('G')-1) * TIME_HOUR + (int)date('i') * TIME_MINUTE, // donderdag (4) is eerste dag van unix time
    //     ),
    //     array(
    //       'test'  => 'Vandaag, wat later dan nu, geweest, moet morgen',
    //       'every' => 'week '.date('w H:i'),
    //       'last'  => time() + rand(TIME_MINUTE,TIME_HOUR),
    //       'next'  => mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')) + TIME_WEEK,
    //     ),
    //   );
    //   foreach ($tests as $test) {
    //     $result = $this->CI->cronjob->_calc_next( $test['every'], $test['last'] );
    //     $this->assertEquals( $test['next'], $result, '['.$test['test'].'] '.unix_to_normal($result). ' zou '. unix_to_normal($test['next']).' moeten zijn. ( every=>`'.$test['every'].'`, last=>'.unix_to_normal($test['last']).')' );
    //   }
    // }
    

    // public function test_calc_next_month()  {
    //   $tests = array(
    //     // Wanneer is volgende (net geweest)
    //     array(
    //       'test'  => 'Precies vandaag geweest, moet volgende maand',
    //       'every' => 'month '.date('j H:i'),
    //       'last'  => time(),
    //       'next'  => mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')) + TIME_DAY * date('t'),
    //     ),
    //     array(
    //       'test'  => 'Vandaag, wat later dan nu geweest, moet volgende maand',
    //       'every' => 'month '.date('j H:i'),
    //       'last'  => time() + rand(TIME_MINUTE, 2*TIME_HOUR),
    //       'next'  => mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')) + TIME_DAY * date('t'),
    //     ),
    //   );
    //   foreach ($tests as $test) {
    //     $result = $this->CI->cronjob->_calc_next( $test['every'], $test['last'] );
    //     $this->assertEquals( $test['next'], $result, '['.$test['test'].'] '.unix_to_normal($result). ' zou '. unix_to_normal($test['next']).' moeten zijn. ( every=>`'.$test['every'].'`, last=>'.unix_to_normal($test['last']).')' );
    //   }
    // }
    
    
    // public function test_needs_run() {
    
    //   // Elke minuut
    //   $job = array(
    //     'name'      => 'elke minuut',
    //     'last'      => time(),
    //     'every'     => '1'
    //   );
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertLessThanOrEqual( $job['last'] + 60 , $job['next'] );
    //   $this->assertEquals( false, $run );
    
    //   $job['last'] = time()-60;
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertLessThanOrEqual( $job['last'] + 60, $job['next'] );
    //   $this->assertEquals( true, $run );
    
    
    //   // Elke dag
    //   $job = array(
    //     'name'      => 'elke dag',
    //     'last'      => time(),
    //     'every'     => 'day '.date('H:i')
    //   );
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertEquals( false, $run );
    
    //   $job['last'] = time() - TIME_DAY;
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertEquals( true, $run );
    
    //   // Elke week
    //   $job = array(
    //     'name'      => 'elke week',
    //     'last'      => time(),
    //     'every'     => 'week '.date('w H:i')
    //   );
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertEquals( false, $run );

    //   $job['last'] = time() - TIME_DAY;
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertEquals( false, $run );
    
    //   $job['last'] = time() - TIME_WEEK;
    //   $job['next'] = $this->CI->cronjob->_calc_next( $job['every'], $job['last'] );
    //   $run = $this->CI->cronjob->job_needs_run($job);
    //   $this->assertEquals( true, $run );
    
    // }




}

?>