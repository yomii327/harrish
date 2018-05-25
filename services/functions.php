<?php

/****************************************************************
 * This class includes all custom and mysql defined methods     *
 * @Author: Muhammad Sajid                                      *
 * @Email: webspot17530@gmail.com                               *
 * @Date: Wed Oct 26 2011                                       *
 * @Permission: Opensource                                      *
 ****************************************************************/

/*****************************
	 * All Constants Define Here *
******************************/

// DB tables name

define('COMPANIES', 'pms_companies');
define('BUILDERS', 'user');
define('BUILDER', 'pms_builders');
define('DEFECTS', 'pms_defects');
define('OWNERS', 'pms_owners');
define('PROJECTS', 'user_projects');
define('RESPONSIBLES', 'pms_responsibles');
define('ASSIGN', 'pms_assign');	
define('SUBBUILDERS', 'pms_builder_to_subbuilders');
define('DEFECTSLIST', 'pms_defects_list');
define('PROJECTDEFECTS', 'pms_pro_defects');
define('PROJECTLOCATION', 'project_locations');

// Website name
define('SITE_NAME', 'wiseworker | DefectID');

// Application name
define('APPS_NAME', 'DEFECTID');

// Localhost settings	
define('SITE_PATH', 'dev/defectid/');

// System admin email addsess
//define('EMAIL', 'webspot49@gmail.com');
define('EMAIL', 'info@wiseworker.net');

// Redirect URL
//define('DOMAIN', $_SERVER['SERVER_NAME']."/".SITE_PATH);
define('DOMAIN', "50.56.209.23/".SITE_PATH);
//echo DOMAIN;
// Image path for pdf
define('IMG_SRC', 'http://'.DOMAIN);

/*if($_SERVER['SERVER_NAME']=='localhost')
	define('DOMAIN', $_SERVER['SERVER_NAME']."/".SITE_PATH);
else
	define('DOMAIN', $_SERVER['SERVER_NAME']);*/
	
//$p = pathinfo($_SERVER["REQUEST_URI"]);

// Access denied varaibles
define('ACCESS_DENIED_SCREEN', "http://".DOMAIN."pms.php?sect=access_denied");

// Company variables
define('COMPANY_ANALYSIS', "http://".DOMAIN."pms.php?sect=c_full_analysis");
define('COMPANY_DASHBOARD', "http://".DOMAIN."pms.php?sect=c_dashboard");

// Builder variables
define('REQ_SUCC', "Location: http://".DOMAIN."pms.php?sect=log&type=apply_now");
define('BUILDER_ANALYSIS', "http://".DOMAIN."pms.php?sect=b_full_analysis");
define('BUILDER_DASHBOARD', "http://".DOMAIN."pms.php?sect=b_dashboard");
define('HOME_SCREEN', "http://".DOMAIN);
define('SHOW_PROJECTS', "http://".DOMAIN."pms.php?sect=show_project");

// Owner variables
define('OWNER_DASHBOARD', "http://".DOMAIN."pms.php?sect=o_dashboard");

// Account Activate location
define('ACTIVATE', "http://".DOMAIN."activate/");

// Responsible variables
define('RESPONSIBLE_DASHBOARD', "http://".DOMAIN."pms.php?sect=r_dashboard");
define('SHOW_ASSIGN_TO', "http://".DOMAIN."pms.php?sect=assign_to");
	
class DB_Class
{
	//url: http://wiseworker.net:2082/cpsess8234986444/frontend/x3/index.html
    public $dbPath = 'localhost';
	//public $dbUser = 'wisework_fxbytes'; // Live details
	public $dbUser = 'wisework_fxbytes';
	//public $dbPass = 'WebSpot49'; live details
	public $dbPass = 'fxbytes!@#';
	//public $dbPass = 'fxbytes!@#';
	public $dbName = 'defectid';
	//public $dbName = 'fxdev';
	public $dbConn = '';
	public $query = '';

	/*****************************************************************************
	 *                                 Constructor                               *
	 *****************************************************************************/
	function __construct() {
		$this->dbConn = mysql_connect($this->dbPath,$this->dbUser,$this->dbPass) or die('Server Not Found 404');
		mysql_select_db($this->dbName,$this->dbConn);
	}
	
	/*****************************************************************************
	 *                             Methods Decelaration                          *
	 *****************************************************************************/
    // Send a MySQL query
	function db_query($q){
		return mysql_query($q);
	}
	
	// Get a result row as an enumerated array
	function db_fetch_row($q){
		return mysql_fetch_row($q);
	}
	
	// Fetch a result row as an associative array
	function db_fetch_assoc($q){
		return mysql_fetch_assoc($q);
	}
	
	// Get number of rows in result
	function db_num_rows($q){
		return mysql_num_rows($q);
	}
	
	// Get number of fields in result
	function db_num_fields($q){
		return mysql_num_fields($q);
	}
	
	// Get the ID generated in the last query
	function db_insert_id(){
		return mysql_insert_id();
	}
	
	// Returns the text of the error message from previous MySQL operation
	function db_error(){
		return mysql_error();
	}
	 
	/*****************************************************************************
	 *                           Custome Methods                                 *
	 *****************************************************************************/	
	function db_select_all($table, $where){
		return mysql_query("SELECT * FROM ".$table." WHERE ".$where);
	}
	
	function db_delete($table, $id){
		return mysql_query("DELETE FROM ".$table." WHERE id = ".$id);
	}
	
	function rendom($l=8){
		$src = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz0987654321aeiou';
		$len = strlen($src) - 1;
		$str = '';
		for($i=0; $i<$l; $i++) {
			$x = rand(0,$len);
			$str .= $src[$x];
		}
		return $str;
	}
	
	function rendomNum($l){
		$src = '1234567890';
		$len = strlen($src) - 1;
		$str = '';
		for($i=0; $i<$l; $i++) {
			$x = rand(0,$len);
			$str .= $src[$x];
		}
		return $str;
	}
	
	function upload_file($n, $t, $d){
		if(!is_dir($d))
			mkdir($d);
	
		$myfile = explode('.', $n);
		$file = md5($this->rendom());
		$e = 'jpg';
		$myFileName = $file .'.'. $e;
		$photo = $d . $myFileName;
		move_uploaded_file($t, $photo);
		return $photo;
	}
	
	function create_file($fn=NULL, $e, $data, $d){
		if($fn==NULL)
			$fn = $this->rendom();
		
		if(!is_dir($d))
			mkdir($d);
		
		$tempFile = "$d/$fn.$e";
		$fh = fopen($tempFile, 'w') or die("can't open file");
		fwrite($fh, $data);
		fclose($fh);
		return $tempFile;
	}
	
	function br2nl($input){
		return preg_replace('/<br(\s+)?\/?>/i', "\n", $input);
	}
	
	function send_mail($detail){
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= "From: ".$detail['name']." <".$detail['from'].">" . "\r\n";
		//$headers .= 'Cc: bd@enviro-source.net' . "\r\n";
		//$headers .= 'Bcc: webspot17530@gmail.com' . "\r\n";
		
		if(mail($detail['to'], $detail['subject'], $detail['msg'], $headers)){
			return 0;
		}else{
			return 1;
		}		
	}
	
	/**
	*
	*  PHP validate email
	*  http://www.webtoolkit.info/
	*
	**/	 
	function isValidEmail($email){
		
		return preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email);
	}
	
	function truncate_text($text, $char, $append='...') {
		if(strlen($text) > $char) {
			$text = substr($text, 0, $char);
			$text .= $append;
		}
		return $text;
	}
	
	/**
	* Backup mysql database OR just a table
	**/
	function backup_tables($tables = '*'){
		$return = '';
		$rollUp = date("d-m-y H-i-s", time());
	
		//get all of the tables
		if($tables == '*'){
			$tables = array();
			$result = mysql_query('SHOW TABLES');
			while($row = mysql_fetch_row($result)){
				$tables[] = $row[0];
			}
		}else{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
	
		//cycle through
		foreach($tables as $table){
			$result = mysql_query('SELECT * FROM '.$table);
			$num_fields = mysql_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysql_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						//$row[$j] = preg_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
	
		//save file
		//$handle = fopen('db-backup-'.$rollUp.'-'.(md5(implode(',',$tables))).'.sql','w+');
		$handle = fopen('mysql/'.$rollUp.'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
	}
	
	
	
	
	
	
}
?>