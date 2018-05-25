<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

$builder_id=$_SESSION['ww_builder_id'];
$pid=base64_decode($_REQUEST['pid']);
$hb=base64_decode($_REQUEST['hb']);

$q="SELECT * FROM ".PROJECTS." p 
	LEFT JOIN ".BUILDERS." b ON p.user_id=b.user_id 
	WHERE p.project_id='$pid' AND p.user_id='$hb' ";
	//echo $q; die;
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
// get project info
$f=$obj->db_fetch_assoc($obj->db_query($q));
$pro_name=stripslashes($f['project_name']);
$pro_code=stripslashes($f['pro_code']);
$builder_full_name=stripslashes($f['user_fullname']);

// get issues info
$ad=array();
$fd_info="";
$fd_info_hd="";

$qd="SELECT status,	COUNT(df_id) AS total FROM ".DEFECTS." WHERE project_id='$pid' GROUP BY status ORDER BY total DESC";
if($obj->db_num_rows($obj->db_query($qd))>0){
	$rd=$obj->db_query($qd);
	$i=0;
	while($fd=$obj->db_fetch_assoc($rd)){
		$ad[$i]['status']=$fd['status'];
		$ad[$i]['total']=$fd['total'];
		$i++;
	}
	
	for($i=0;$i<count($ad);$i++){
		$fd_info.=$ad[$i]['status'].' : '.$ad[$i]['total'].'<br />';
	}
	
	$fd_info_hd="[ Total: ".($ad[0]['total'] + (isset($ad[1])?$ad[1]['total']:0) )." ]";
	
}else{
	$fd_info_hd="[ Total: 0 ]";
}

// get total issues against a trade
$ft_info="";
$ft_info_hd="";
$at=array();

$qt="SELECT r.resp_comp_name, SUM(IF(d.status = 'Open',1,0)) AS open, 
	SUM(IF(d.status='Closed',1,0)) AS closed FROM ".DEFECTS." d, ".RESPONSIBLES." r 
	WHERE d.project_id='$pid' AND r.resp_id=d.resp_id GROUP BY d.resp_id";
	
$rows=$obj->db_num_rows($obj->db_query($qt));

if($rows>0){
	$rt=$obj->db_query($qt);

	$ft_info="<table width='100%'>";
	
	while($ft=$obj->db_fetch_assoc($rt)){
		$ft_info.="<tr><td width='50%'>".stripslashes($ft['resp_comp_name'])."</td>
					   <td>[ Open ".$ft['open']." ]</td>
					   <td>[ Closed ".$ft['closed']." ]</td>
					   <td>[ Total ".($ft['open']+$ft['closed'])."]</td></tr>";
	}	
	
	$ft_info.="</table>";
	$ft_info_hd="[ Total: ".$rows." ]";
}else{
	$ft_info_hd="[ Total: 0 ]";
}

// get inspectors info
$fi_info="";
$fi_info_hd="";
$ai=array();

$qi="SELECT owner_full_name FROM ".OWNERS." WHERE ow_project_id='$pid'";
if($obj->db_num_rows($obj->db_query($qi))>0){
	
	$ri=$obj->db_query($qi);

	while($fi=$obj->db_fetch_assoc($ri)){
		array_push($ai,$fi);
	}
	
	$fi_info="<table width='100%'>";
	
	for($i=0;$i<count($ai);$i++){
		$fi_info.="<tr><td>".stripslashes($ai[$i]['owner_full_name'])."</td></tr>";
	}
	
	$fi_info.="</table>";
	$fi_info_hd="[ Total: ".count($ai)." ]";
}else{
	$fi_info_hd="[ Total: 0 ]";
}

// get associated managers
$fm_info="";
$fm_info_hd="[ Total: 0 ]";
$am=array();

$qm="SELECT b.user_fullname, b.company_name FROM ".BUILDERS." b LEFT JOIN ".SUBBUILDERS." sb ON b.user_id=sb.sb_id WHERE fk_b_id='$hb' AND fk_p_id='$pid'";
//echo $qm; die;
$fm_rows=$obj->db_num_rows($obj->db_query($qm));
if($fm_rows>0){
	
	$rm=$obj->db_query($qm);
	
	$fm_info="<table width='100%'>";
	
	while($fm=$obj->db_fetch_assoc($rm)){
		$fm_info.="<tr><td width='50%'>".stripslashes($fm['user_fullname'])."</td>
					   <td>".stripslashes($fm['company_name'])."</td></tr>";
	}	
	
	$fm_info.="</table>";
	
	$fm_info_hd = "[ Total: $fm_rows ]";
}
?>
<!-- Accordion resource files -->
<link type="text/css" href="accordion/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="accordion/js/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript">
$(function(){
	// Accordion
	$("#accordion").accordion({ header: "h3" }); 	
});
</script>
<!--// Accordion resource files -->
<div class="content_container">
	<div class="content_left" style="width:100%;">
		<div class="content_hd1" style="background-image:url(images/detailed_project_report_hd.png);"></div>
		
		<?php include'includes/b_analysis_panel.php'; ?>
		
		<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td style="color:#FFFFFF; font-size:16px; font-weight:bold; font-family:Arial;">
					Name: <?=$pro_name?> | ID: <?=$pro_code?> | Head by: <?=$hb==$builder_id?'Own':$user_fullname?>
				</td>
			</tr>
		</table>
			
		<div class="signin_form" style="margin:0 auto; width:100%;">
			<div id="accordion">
				<div>		
					<h3><a href="#" class="a_accordion">Inspections <?=$fd_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; max-height:25px; overflow:hidden;"><?=$fd_info?></div>
				</div>
				<div>
					<h3><a href="#" class="a_accordion">Total inspections against a trade <?=$ft_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; overflow:scroll; max-height:150px;"><?=$ft_info?></div>
				</div>				
				<div>
					<h3><a href="#" class="a_accordion">Inspectors <?=$fi_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; overflow:scroll; max-height:150px;"><?=$fi_info?></div>
				</div>
				<div>
					<h3><a href="#" class="a_accordion">Associated managers <?=$fm_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; overflow:scroll; max-height:150px;"><?=$fm_info?></div>
				</div>				
			</div>
		</div>
		
	</div>	
</div>