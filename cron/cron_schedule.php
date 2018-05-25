<?php
ob_start();
include(dirname(__FILE__).'/../includes/commanfunction.php');
require(dirname(__FILE__).'/../fpdf/mc_table.php');
require_once(dirname(__FILE__).'/../includes/class.phpmailer.php');
define("STOREFOLDER", dirname(__FILE__).'/deadlock', true);

#Make directory when if not exists.
if(!is_dir(STOREFOLDER)){
	@mkdir(STOREFOLDER, 0777);
}
//error_reporting(E_ALL); 	//ini_set('display_errors', 1);		//Function Section End Here
@unlink(STOREFOLDER.'/open_insp_proj.txt');	 

require_once('cron.class.php');

class CronSchedule extends cron {
	#Define variables.
	private $day;
	private $month;
	private $hours;
	private $minute;
	private $today;
	private $output;
	
	public function __construct(){
		$this->day = date('N'); //Numeric representation of the day of the week(1=Mon to 7=Sun).
		$this->month = date('n'); //Numeric representation of a month, without leading zeros.
		$this->hours = date('H'); //24-hour format of an hour with leading zeros.
		$this->minute = date('i'); //Minutes with leading zeros.
		$this->today = date('Y-m-d'); //Today date.
	}
	
	#Get Cron Schedule data
	public function getCronSchedule(){
		$this->output = array();
		$query = 'SELECT * FROM cron_schedule WHERE status = \'active\' AND is_deleted =0';
		$result = mysql_query($query);
		$records = mysql_num_rows($result);
		if($records>0){
			while($rows = mysql_fetch_assoc($result)){
				$this->output[] = $rows;
			}
		}
		return $this->output;
	}//End of getCronSchedule().
	
	#Run cron.
	public function execute(){		
		#echo 'Day='.$this->day.', Month='.$this->month.', Hours='.$this->hours.', Minute='.$this->minute.', Date='.$this->today.'<br>';
		#Get cron schedule data.
		$scheduleData = $this->getCronSchedule();		
		#echo '<pre>';print_r($scheduleData);die;
		
		$this->output = array('error'=>'false', 'message'=>'Cron file executed successfully.');
		if(isset($scheduleData) && !empty($scheduleData)) {
			$result = array();
			foreach($scheduleData as $sRows) {
				#echo $sRows['start_date'].'--'.strtotime($sRows['start_date']).'==='.strtotime($this->today).'<br>';
				if(strtotime($this->today) >= strtotime($sRows['start_date'])) {
					echo $sRows['start_hour'].':'.$sRows['start_minute'].'==='.$this->hours.':'.$this->minute.'<br>';
					#$result[] = $this->executeFunction($sRows['function_name']);
					if($sRows['start_week_day'] == 0) {//Run everyday
						#echo $sRows['start_hour'].''.$sRows['start_minute'].'<br>';
						if($sRows['start_hour']==$this->hours && $sRows['start_minute']==$this->minute){
							#include file.
							$result[] = $this->executeFunction($sRows['function_name']);
						}
					} elseif($sRows['start_week_day'] == $this->day) {//Run weekday
						#echo $sRows['start_hour'].''.$sRows['start_minute'].'<br>';
						if($sRows['start_hour']==$this->hours && $sRows['start_minute']==$this->minute){
							#include file.
							$result[] = $this->executeFunction($sRows['function_name']);
						}
					} else {
						$result[] = array('error'=>'false', 'message'=>'Email scheduled not found!');
					}
				} else {					
					$result[] = array('error'=>'false', 'message'=>'Email scheduled not found!');
				}
			}//End of foreach.
			$result = (!empty($result))? $result : array('error'=>'false', 'message'=>'Cron file executed successfully.');
			$this->output = $result;
		} else {
			$this->output = array('error'=>'true', 'message'=>'Records Not Found!');
		}
		#Return final output.
		return json_encode($this->output);
	}//End of execute().
	
	#Execute functions.
	public function executeFunction($functionName) {
		switch($functionName) {
			case 'dailyOpenInspection':
				$result = $this->dailyOpenInspection();
				$this->output = array('error'=>'false', 'message'=>$result);
				break;
			case 'dailyFixedInspection':
				$result = $this->dailyFixedInspection();
				$this->output = array('error'=>'false', 'message'=>$result);
				break;
			default:
				$this->output = array('error'=>'true', 'message'=>'Function Not Found!');
		}
		return $this->output;
	}
	
	#Testing function.
	public function testCron($email){
		$this->output = $this->dailyOpenInspection($email);
		echo '<br>Daily Open Inspection<br>'.$this->output.'<br><br>';
		$this->output = $this->dailyFixedInspection($email);
		echo '<br>Daily Fixed Inspection<br>'.$this->output.'<br><br>';
	}
}//End of class.

#Create object.
$obj = new CronSchedule;
#$obj->testCron('lanjhesh.manmohan@fxbytes.com');die;
$result = $obj->execute();
echo $result;

/* Omit PHP closing tags to help avoid accidental output */
