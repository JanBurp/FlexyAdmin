<?php

class CronjobTest extends CIUnit_Framework_TestCase {

    protected function setUp () {
      $this->CI->load->model('cronjob');
      $this->CI->load->helper('date');
    }

    public function test_calc_next_minutes()  { 
      // every x minutes
      $this->assertGreaterThan( time(), $this->CI->cronjob->_calc_next(5,time()) , 'Moet 5 minuten later zijn' );
      $this->assertGreaterThan( time(), $this->CI->cronjob->_calc_next(1,time()) , 'Moet 1 minuut later zijn' );
      $this->assertGreaterThanOrEqual( time(), $this->CI->cronjob->_calc_next(0,time()) , 'Moet gelijk zijn' );
    }

    public function test_calc_next_day()  { 
      // day 12:00
      $testtime=mktime(12,0,0); // 12:00
      $this->assertLessThan( $testtime, $this->CI->cronjob->_calc_next('day 11:59',$testtime) ,    '11:59 moet eerder zijn dan 12:00 ('.$testtime.')' );
      $this->assertGreaterThanOrEqual( $testtime, $this->CI->cronjob->_calc_next('day 12:00',$testtime) , '12:00 moet later/gelijk zijn dan 12:00 ('.$testtime.')' );
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('day 12:01',$testtime) , '12:01 moet later/gelijk zijn dan 12:00 ('.$testtime.')' );
      // day 23:59
      $testtime=mktime(23,59,0); // 23:59
      $this->assertLessThan( $testtime, $this->CI->cronjob->_calc_next('day 23:00',$testtime) ,    '23:00 moet eerder zijn dan 23:59 ('.$testtime.')' );
      $this->assertGreaterThanOrEqual( $testtime, $this->CI->cronjob->_calc_next('day 23:59',$testtime) , '23:00 moet later/gelijk zijn dan 23:59 ('.$testtime.')' );
      $this->assertLessThan( $testtime, $this->CI->cronjob->_calc_next('day 0:00',$testtime) , '0:00 moet eerder zijn dan 23:59 ('.$testtime.')' );
      $testtime=mktime(23,59,0,date('n'),date('j')-1); // vorige dag
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('day 0:00',$testtime) , '0:00 (dag later) moet later zijn dan 23:59 ('.$testtime.')' );
    }

    public function test_calc_next_week()  { 
      // last sunday 23:59
      $date=getdate();
      $wday=$date['wday']; // 0 = sunday
      $testtime=mktime( 23,59,0, date('n'), date('j')-$wday );
      $human=unix_to_mysql($testtime);
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('week 6 23:59',$testtime-86400) , 'zaterdag 0:00 moet eerder zijn dan '.$human );
      $this->assertLessThan( $testtime, $this->CI->cronjob->_calc_next('week 0 23:00',$testtime) ,    'zondag 23:00 moet eerder zijn dan '.$human );
      $this->assertGreaterThanOrEqual( $testtime, $this->CI->cronjob->_calc_next('week 0 23:59',$testtime) , 'zondag 23:59 moet later/gelijk zijn dan '.$human );
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('week 1 0:00',$testtime) , 'maandag 0:00 moet later zijn dan '.$human );
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('week 1 12:00',$testtime) , 'maandag 12:00 moet later zijn dan '.$human );
    }

    public function test_calc_next_month()  { 
      // eerste dag vd maand 23:59
      $date=getdate();
      $mday=$date['mday']-1; // eerste dag vd maand
      $testtime=mktime( 23,59,0, date('n'), date('j')-$mday );
      $human=unix_to_mysql($testtime);
      $this->assertLessThan( $testtime, $this->CI->cronjob->_calc_next('month 1 0:00',$testtime) , '1e dag vd maand 0:00 moet eerder zijn dan '.$human );
      $this->assertLessThan( $testtime, $this->CI->cronjob->_calc_next('month 1 23:00',$testtime) ,    '1e dag vd maand 23:00 moet eerder zijn dan '.$human );
      $this->assertGreaterThanOrEqual( $testtime, $this->CI->cronjob->_calc_next('month 1 23:59',$testtime) , '1e dag vd maand 23:59 moet later/gelijk zijn dan '.$human );
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('month 2 0:00',$testtime) , '2e dag vd maand 0:00 moet later zijn dan '.$human );
      $this->assertGreaterThan( $testtime, $this->CI->cronjob->_calc_next('month 2 12:00',$testtime) , '2e dag vd maand 12:00 moet later zijn dan '.$human );
    }




}

?>