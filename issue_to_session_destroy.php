<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

if($_SESSION['project_id_issue'])
{
	unset($_SESSION['project_id_issue']);
	
}

?>
