<?php
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

$c_id=$_SESSION['ww_c_id'];
$id=base64_decode($_GET['id']);

$q="SELECT * FROM ".PROJECTS." p 
	LEFT JOIN ".BUILDERS." b ON p.user_id=b.user_id 
	WHERE p.project_id='$id' ";
	
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
// get project info
$f=$obj->db_fetch_assoc($obj->db_query($q));

// get issues info
$ad=array();
$fd_info="";
$fd_info_hd="";
$total_issues=0;

$qd="SELECT status, COUNT(df_id) AS total FROM ".DEFECTS." WHERE project_id='$id' GROUP BY status ORDER BY total DESC";
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
		$total_issues=$total_issues + $ad[$i]['total'];
	}
	
	$fd_info_hd="[ Total: ".$total_issues." ]";
	
}else{
	$fd_info_hd="[ Total: 0 ]";
}

// get total trades and issues(open/closed) against a trade
$ft_info="";
$ft_info_hd="";
$at=array();

$qt="SELECT r.resp_comp_name, SUM(IF(d.status = 'Open',1,0)) AS open, 
	SUM(IF(d.status='Closed',1,0)) AS closed FROM ".DEFECTS." d, ".RESPONSIBLES." r 
	WHERE d.project_id='$id' AND r.resp_id=d.resp_id GROUP BY d.resp_id";
	
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
$qi="SELECT owner_full_name FROM ".OWNERS." WHERE ow_project_id='$id'";
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
	$fi_info_hd="[ Total: 0]";
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
		
		<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td style="color:#FFFFFF; font-size:16px; font-weight:bold; font-family:Arial;">
					Name: <?=stripslashes($f['project_name'])?> | ID: <?=$f['pro_code']?> | Manager: <?=stripslashes($f['user_fullname'])?>
				</td>
			</tr>
		</table>
			
		<div class="signin_form" style="margin:0 auto; width:100%;">
			<div id="accordion">
				<div>		
					<h3><a href="#" class="a_accordion">Inspections <?=$fd_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; max-height:65px; overflow:hidden;"><?=$fd_info?></div>
				</div>
				<div>
					<h3><a href="#" class="a_accordion">Total inspections against a trade <?=$ft_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; overflow:scroll; max-height:150px;">
					<?=$ft_info?>
					</div>
				</div>				
				<div>
					<h3><a href="#" class="a_accordion">Inspectors <?=$fi_info_hd?></a></h3>
					<div style="text-shadow:none; font-size:13px; overflow:scroll; max-height:150px;"><?=$fi_info?></div>
				</div>				
			</div>
		</div>
		
	</div>	
</div>