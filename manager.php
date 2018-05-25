<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('adminmodel');
		$this->load->model('managermodel');		
		$this->load->library('commanfunction');
	//	if($this->session->userdata('userType')!="manager"){
		if($this->session->userdata('userType')=="company"){
			redirect("home/",'refresh');
		}		

	}

# Load Home Page.
	public function index(){
		$this->loginExists();
		//$data['publicUrl']=$this->config->item('public_url');
		//$this->load->view('reports',$data);
		$userType = $this->session->userdata('userType');
		if($userType=="manager"){
			redirect($userType."/projectList/",'refresh');
		}else{
			$this->session->set_userdata('isSubConUSer', $isSubConUSer);
			if($isSubConUSer == 1){
				redirect("/safetyWalk/searchResult", 'refresh');
			}else{
				redirect($userType."/projectList/", 'refresh');
			}
		}
	}

# Show Company Profile.
	public function projectList(){
		$this->loginExists();			 		
		$this->load->view('manger_projectList');
	}		

# Project Add / Edit by Manager.
	public function projectSection($projId = 0){
		$this->loginExists();
		$userid = $this->session->userdata('uid');

		// Set Validation Rules
		$rules = array();
		if($projId==0){
			$rules[] = array("field"=>"name", "label"=>"Project Name", "rules"=>"required|min_length[3]|callback_projectExists" );
		}		
		$rules[] = array("field"=>"site_manager", "label"=>"Site Manager", "rules"=>"required|min_length[3]" );
		$rules[] = array("field"=>"type", "label"=>"Project Type", "rules"=>"required" );
		$rules[] = array("field"=>"address_line1", "label"=>"Address Line 1", "rules"=>"required" );
		$rules[] = array("field"=>"address_line2", "label"=>"Address Line 2", "rules"=>"" );
		$rules[] = array("field"=>"suburb", "label"=>"Suburb", "rules"=>"required|min_length[3]" );
		$rules[] = array("field"=>"postcode", "label"=>"Postcode", "rules"=>"required|min_length[3]" );						
		$rules[] = array("field"=>"state", "label"=>"State", "rules"=>"required" );
		$rules[] = array("field"=>"country", "label"=>"Country", "rules"=>"required" );			
		
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters("","<br>");
		//check validation
		if($this->form_validation->run() == true){
			$secKey = $this->session->userdata("sKey");
			if($secKey!=$this->input->post('secKey')){
				redirect("manager/projectList/",'refresh');
			}
			//Project Add / Edit 
			$this->managermodel->add_edit_projectSection($projId);

			$this->session->set_userdata("secKey",'');		
			redirect("manager/projectList/",'refresh');
	 	}
		if($projId!=0){ $data['projId'] = $projId;
			$results = $this->managermodel->getProjectDetailsForProjectSection($projId);
			if($results==false){ redirect("manager/projectList/",'refresh');	}
			$data['projD'] = $results[0];
		}else{	$data['projId'] = '';	}

		$this->load->view('manger_projectSection',$data);
	}			

# Show Project Profile.
	public function projectProfile($projId=0){
		$this->loginExists();
		
		$results = $this->managermodel->getProjectProfileDetails($projId);
		if($results==false){ redirect("manager/projectList/",'refresh');	}	
		$data['projD'] = $results[0];
		$this->load->view('manger_projectProfile',$data);	
	}
		
# Check user exists or not by email or username	
	public function projectExists(){
		return $this->managermodel->checkProjectExists();
	}
	
# Ajax Section : Check user exists or not by email or username
	public function projectExistsByAjax(){
		$result = $this->projectExists();
		if($result==true){	echo 0;	}else{ echo 1;}
	}

# Project DataGrid Function 
	public function projectListByDataGrid(){
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$userid = $this->session->userdata('uid');
	
	 
	$aColumns = array('project_name', 'project_type', 'project_address_line1', 'project_suburb', 'project_state', 'project_postcode', 'project_country', 'project_is_deleted', 'proj.project_id');

	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "proj.project_id";
	
	/* DB table to use */
	$sTable2 = "user_projects as up";	
	$sTable = "projects as proj";
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
		$sWhere =" up.user_id=".$this->session->userdata('uid')." and proj.project_is_deleted!=1 ";
		$result = $this->commanfunction->ListByDataGridWithJoinTable($aColumns, $sIndexColumn, $sTable, $sTable2,$sWhere,$_GET,9);
		$userType = $this->session->userdata('userType');
		$userRoll = $this->session->userdata('userRoll');
		/*echo '<pre>';
		echo $userType.'<br>';
		print_r($userRoll);
		die;*/
		$perm = $this->session->userdata('perm');
		$perm = (isset($perm) && !empty($perm))?$perm:array();
	/* Send Output */

	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $result['iTotal'],
		"iTotalDisplayRecords" => $result['iFilteredTotal'],
		"aaData" => array()
	);
	
		while ( $aRow = mysql_fetch_array( $result['rResult'] ) )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] == "version" )
				{
					/* Special output formatting for 'version' column */
					$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
				}
				else if ( $aColumns[$i] != ' ' )
				{
					$aColumns[8]=str_replace("proj.","",$aColumns[8]);
					/* General output */
					if($i==0){
						if($userType!="user"){
							$data ="<a  title='Edit' href='".base_url()."manager/projectSection/".$aRow[ $aColumns[8] ]."'><img src='".base_url()."swidpublice/swidTheme/images/edit.png' border='none' height='16' /></a> ";
						}else if($userType=="user" && $userRoll[$aRow[$aColumns[8]]]['userRoll'] == "UserAdmin"){
							$data ="<a  title='Edit' href='".base_url()."manager/projectSection/".$aRow[ $aColumns[8] ]."'><img src='".base_url()."swidpublice/swidTheme/images/edit.png' border='none' height='16' /></a> ";
						}else{
							$data ="";
						}
						$row[] =  $data.str_replace('\"','"',str_replace("\'","'",$aRow[ $aColumns[$i] ]));
					}elseif($i==7){
						
						if($aRow[$aColumns[7]]==0){
							if($userType!="user"){
								$row[] ="<a title='No' href='#' onclick='return archiveRecord(".$aRow[$aColumns[8]].",2);' style='padding-left:20px;'>No</a>";
							}else{
								$row[] ="No";
							}
						}else{
							if($userType!="user"){
								$row[] ="<a title='Yes' href='#' onclick='return archiveRecord(".$aRow[$aColumns[8]].",0);' style='padding-left:20px;'>Yes</a>";
							}else{
								$row[] ="Yes";
							}
						}
					}elseif($i==8){
					if($userType=="user"){
						$data = "<a  title='View'  href='".base_url()."manager/projectProfile/".$aRow[ $aColumns[$i] ]."'><img src='".base_url()."swidpublice/swidTheme/images/small_view.png' border='none' height='20'  /></a>&nbsp;&nbsp;";
						$data.= "<a  title='Project Configuration'  href='".base_url()."associateUsers/index/".$aRow[ $aColumns[$i] ]."'><img src='".base_url()."swidpublice/swidTheme/images/small_project_configuration.png' border='none' height='20'  /></a>";
					}else{
						$data = "<a  title='View'  href='".base_url()."manager/projectProfile/".$aRow[ $aColumns[$i] ]."'><img src='".base_url()."swidpublice/swidTheme/images/small_view.png' border='none' height='20'  /></a>&nbsp;&nbsp;
						<a  title='Delete' href='#'><img src='".base_url()."swidpublice/swidTheme/images/small_close_new.png' border='none' onclick='return deleteRecord(".$aRow[$aColumns[$i]].");' height='20'  /></a></a>&nbsp;&nbsp;";
					}
						if($aRow[$aColumns[7]]==2){
							if($userType!="user"){
								$data.= "<a  title='Project Configuration'  href='#' onclick='alert(\"Please activate this project then try for project configuration.\");'><img src='".base_url()."swidpublice/swidTheme/images/small_project_configuration.png' border='none' height='20'  /></a>";
							}
							if($userType!="user" || (isset($perm['siteDiary']) && $perm['siteDiary']==0)){
								$data.="&nbsp;&nbsp;<a title='Daily Diary'  href='#' onclick='alert(\"Please activate this project then try for project configuration.\");'><img src='".base_url()."swidpublice/swidTheme/images/small_site_diary.png' border='none' height='20'  /></a>";
							}
						}else{
							if($userType!="user"){
								$data.= "<a  title='Project Configuration'  href='".base_url()."manager/projectConfiguration/".$aRow[ $aColumns[$i] ]."'><img src='".base_url()."swidpublice/swidTheme/images/small_project_configuration.png' border='none' height='20'  /></a>";
							}
							
						  if($this->session->userdata('userType') != "user"){
							if(isset($perm['siteDiary']) && $perm['siteDiary']==0){
								$data.="&nbsp;&nbsp;<a title='Daily Diary'  href='".base_url()."dailyDiary/diarySection/".$aRow[ $aColumns[$i] ]."/1'><img src='".base_url()."swidpublice/swidTheme/images/small_site_diary.png' border='none' height='20'  /></a>";
							}
							
							
							if(isset($perm['siteDiary']) && $perm['siteDiary']==2){
								$data.="&nbsp;&nbsp;<a title='Daily Diary'  href='".base_url()."dailyDiary/diarySection/".$aRow[ $aColumns[$i] ]."/1'><img src='".base_url()."swidpublice/swidTheme/images/small_site_diary.png' border='none' height='20'  /></a>";
							}
							
							
							if(!isset($perm['siteDiary'])){
								$data.="&nbsp;&nbsp;<a title='Daily Diary'  href='".base_url()."dailyDiary/diarySection/".$aRow[ $aColumns[$i] ]."/1'><img src='".base_url()."swidpublice/swidTheme/images/small_site_diary.png' border='none' height='20'  /></a>";
							}
						  }
							
							
						}
					
							$row[] = $data;				
						

//					$row[] ="<a href='#'>".$aRow[ $aColumns[$i] ]."</a>";
					}else{
						$row[] =  str_replace('\"','"',str_replace("\'","'",$aRow[ $aColumns[$i] ]));
					}
				}
			}
			$output['aaData'][] = $row;
		}
	
		echo isset($_GET['callback'])?$_GET['callback']:''.''.json_encode( $output ).'';
	}
	
# Delete Project Record
	public function deleteProject($projId=0){
		$this->managermodel->deleteProjectRecord($projId);
		redirect("manager/projectList/",'refresh');
	}

# Archive / UnArchive Project Record
	public function archiveProject($projId=0,$status=0){
		$this->adminmodel->updateDetails("projects",array("project_is_deleted"=>$status, "project_modified"=>"NOW()", "project_modified_by"=>$this->session->userdata('uid')),array("project_id"=>$projId));
		redirect("manager/projectList/",'refresh');
	}
	
# Check Login
	public function loginExists(){
		$ukey = $this->session->userdata('ukey');
		if(empty($ukey)){$this->session->sess_destroy(); redirect('home/');	}
	}

# Project Configuration.
	public function projectConfiguration($projId=0){
		$this->loginExists();	
		$results = $this->managermodel->getProjectDetailsForShowName($projId);
		
		if($results==false){ redirect("manager/projectList/",'refresh');	}	
		$data['projD'] = $results[0];
		$this->session->set_userdata('projId', $projId);
		$data['message']= $this->session->userdata('message');
		$this->session->set_userdata('message','');
		$data['locatList'] = $this->locatTreeView($projId,0); 
		$this->load->view('manger_projectConfiguration',$data);		
	}
	
# Generate Tree View for Location 
	public function locatTreeView($projId=0, $locatId=0, $ajax=false){
	  # 1st Tree Strcture start
		$locatList = $this->adminmodel->getDetails('project_locations',array('project_id'=>$projId, "location_is_deleted"=>0,"location_parent_id"=>$locatId),'location_id as locatId, location_title as locatName');
		
		if($locatList!=false){
		  $data='<ul id="tree_'.$locatId.'">';
		  foreach($locatList as $val){
			# 2nd Tree Strcture start
				$locatList2 = $this->adminmodel->getDetails('project_locations',array('project_id'=>$projId, "location_is_deleted"=>0,"location_parent_id"=>$val['locatId']),'count(location_id) as count');
				$calss="";
				if($locatList2!=false){
					  if($locatList2[0]['count']>0){ $calss="activeLocat"; }else{ $calss="disabLocat";}
			  	}
			# End 2nd Tree Strcture 
			
			$data.='<li class="demo1" id="'.$val['locatId'].'" title="'.$val['locatName'].'"><a href="#" class="'.$calss.'"  id="list_'.$val['locatId'].'" onclick="getLocationList('.$projId.','.$val['locatId'].')">'.$val['locatName'].'</a><div id="treeBox_'.$val['locatId'].'"></div></li>';
		  }
		  $data.='</ul>';		  
	  	}else{$data='';}
		
		# End 1st Tree Strcture 		
			if($ajax==false){
				return $data;
			}else{
				echo $data; die;
			}
	}

# Add Location.
	public function addLocation($projId=0, $type=0){
//		$projId = $this->session->userdata('projId');
		$data['locatId'] = $this->input->post('locatId');
		$data['locatName'] = $this->input->post('locatName');
		if($type==1){
		   $where = array('project_id'=>$projId, "location_title"=>$data['locatName'], "location_parent_id"=>$data['locatId']);
			$results = $this->adminmodel->getDetails('project_locations',$where,'location_id as locId');
			if($results==false){
				$projLocaData["location_title"] = $data['locatName'];
				$projLocaData["location_parent_id"] = $data['locatId'];
				$projLocaData["project_id"] = $projId;
				$projLocaData["location_created"] = "NOW()"; //date('Y-m-d H:i:s');
				$projLocaData["location_created_by"] = $this->session->userdata('uid');	
				$projLocaData["location_modified"] = "NOW()"; //date('Y-m-d H:i:s');	
				$projLocaData["location_modified_by"] = $this->session->userdata('uid');					
				$this->adminmodel->insertDetails('project_locations', $projLocaData);
				//echo '<div style="color:green;">New Sub location added successfully.</div>';	
				echo '<div style="color:green;"></div>';	
			}else{
				echo '<div style="color:red;">Sorry this sub location already exists.</div>';
			}
				exit;
		}else{
			$this->load->view('addLocation', $data);
		}
	}

# Edit Location.
	public function editLocation($projId=0, $type=0){
		$data['locatId'] = $this->input->post('locatId');
		$data['locatName'] = $this->input->post('locatName');
		if($type==1){
			$where = array('project_id'=>$projId, "location_id"=>$data['locatId']);
			$projLocaData["location_title"] = $data['locatName'];
			$projLocaData["location_modified"] = "NOW()"; //date('Y-m-d H:i:s');	
			$projLocaData["location_modified_by"] = $this->session->userdata('uid');	
			$results = $this->adminmodel->updateDetails('project_locations',$projLocaData,$where);
			if($results==false){
				echo '';//'<div style="color:red;">Sorry this location already changed or old.</div>';
			}else{
				//echo '<div style="color:green;">Location updated successfully.</div>';								
			}
			exit;
		}else{
			$this->load->view('editLocation', $data);
		}
	}

# Delete Location.
	public function deleteLocation($projId=0, $type=0){
		$data['locatId'] = $this->input->post('locatId');
		$data['locatName'] = $this->input->post('locatName');
		if($type==1){
			$where = array('project_id'=>$projId, "location_id"=>$data['locatId']);
			$where2 = array('project_id'=>$projId, "location_parent_id"=>$data['locatId']);			
			$projLocaData["location_is_deleted"] = 1;
			$projLocaData["location_modified"] = "NOW()"; //date('Y-m-d H:i:s');
			$projLocaData["location_modified_by"] = $this->session->userdata('uid');	
			$results = $this->adminmodel->updateDetails('project_locations',$projLocaData,$where);
			$results2 = $this->adminmodel->updateDetails('project_locations',$projLocaData,$where2);			
			if($results==false){
				echo '';//'<div style="color:red;">Sorry this location already changed or old.</div>';
			}else{
				// echo '<div style="color:green;">Location deleted successfully with sub location.</div>';								
			}
			exit;
		}
	}	

# Copy Location.
	public function copyLocation($projId=0, $type=0){
		$data['locatId'] = $this->input->post('locatId');
		$data['uniqueId'] = $this->input->post('uniqueId');
		if(isset($data['uniqueId'])){
			$this->session->set_userdata('pasteCount',1); //Set Copy Session
			$where = array('project_id'=>$projId, "location_is_deleted"=>0, "location_parent_id"=>$data['locatId']);
			$results = $this->adminmodel->getDetails('project_locations',$where,'location_id as locId');
//			echo $this->db->last_query();
			if($results==false){
				echo 0;				
			}else{
				echo 1;
			}
			exit;
		}
	}

# Paste Location.
	public function pasteNewLocation($projId=0, $type=0){
		$copyLocation = $this->input->post('copyLocation');
		$pasteLocation = $this->input->post('pasteLocation');		
		$data['uniqueId'] = $this->input->post('uniqueId');
		if(isset($data['uniqueId'])){

			$allCategories = array();
			$allCategories = $this->get_cats($copyLocation);
			if(in_array($pasteLocation, $allCategories)){
				echo 0; //'Error';
			}else{
				$result = $this->pasteLocations($projId, $copyLocation, $pasteLocation);
				if(is_array($result)){
					$pasteCount = $this->session->userdata('pasteCount')+1;
					$this->session->set_userdata('pasteCount',$pasteCount);
					//$_SESSION['pasteCount']++;
					echo 'Location Paste';
				}else{
					echo 'Error in Pasing';
				}
			}

		}
	}	
	
# get Category List
	public function get_cats($cat){
		$i=1;
		$remain = array();
		$all = array();
		$insertArray = array();
		$parentArray = array();
		
		$remain[0] = $cat;
		$all[0] = $cat;	
		while(sizeof($remain)>0){
			$curr = $remain[0];
	//echo "SELECT location_id FROM project_locations WHERE location_parent_id = ".$curr." and location_is_deleted = 0";
			$res = mysql_query("SELECT location_id FROM project_locations WHERE location_parent_id = ".$curr." and location_is_deleted = 0");
			while($row = mysql_fetch_array($res)){
				$all[$i++]=$row['location_id'];
				$remain[sizeof($remain)]=$row['location_id'];
			}
			unset($remain[0]);
			$remain=array_values($remain);
		}
		return $all;
	}	
	
# Paste Location	
	public function pasteLocations($projId, $cat, $pId){
		$i=1;
		$remain = array();
		$all = array();
		$insertArray = array();
		$parentArray = array();
		
		$remain[0] = $cat;
		$title_select="SELECT location_title FROM project_locations WHERE location_id = '".$cat."' and location_is_deleted=0";
		$title_res = mysql_query($title_select);
		if(mysql_num_rows($title_res) > 0){
			$title_obj = mysql_fetch_object($title_res);
			$title = $title_obj->location_title;
			$insert_query = "INSERT INTO project_locations SET
								project_id = '".$projId."',
								location_title = '".addslashes($title)." (Copy ".$this->session->userdata('pasteCount').")',
								location_parent_id = '".$pId."',
							 	location_created = now(),
							 	location_created_by = '".$this->session->userdata('uid')."'";
			mysql_query($insert_query);
			$parentArray[0] = mysql_insert_id();
		}
		
		
		while(sizeof($remain)>0){
			$curr = $remain[0];
			$qSelect = "select location_id from project_locations where location_parent_id = ".$curr." and location_is_deleted = 0";
			$res = mysql_query($qSelect);
			while($row = mysql_fetch_array($res)){
				$all[$i++]=$row['location_id'];
				$remain[sizeof($remain)]=$row['location_id'];
			}
			//
			$newValues = array_diff($all, $insertArray);
			if(!empty($newValues)){
				foreach($newValues as $insertValues){
					$title_select = "SELECT location_title FROM project_locations WHERE location_id = '".$insertValues."' and location_is_deleted = 0";
					$title_res = mysql_query($title_select);
					if(mysql_num_rows($title_res) > 0){
						$title_obj = mysql_fetch_object($title_res);
						$title = $title_obj->location_title;
						
					$insert_query = "INSERT INTO project_locations SET
											project_id = '".$projId."',
											location_title = '".addslashes($title)."',
											location_parent_id = '".$parentArray[0]."',
										 	location_created = now(),
										 	location_created_by = '".$this->session->userdata('uid')."'";
						mysql_query($insert_query);
						
						$parentArray[sizeof($parentArray)] = mysql_insert_id();
						
						$insertArray[] = $insertValues;
					}
				}
			}else{
				#die('Execusion Done');
			}
			//
			array_shift($parentArray);
			array_shift($remain);
		}
		return $all;
	}
	
# Report Section
	public function reports(){
		$this->loginExists();
		$uid = $this->session->userdata('uid');
//		$data['publicUrl']=$this->config->item('public_url');
		$data['prdDetails'] = $this->adminmodel->getDetails('','','','select proj.project_id, proj.project_name, proj.project_state as state FROM projects AS proj RIGHT JOIN user_projects AS up on up.project_id = proj.project_id AND up.uproj_is_deleted=0 WHERE proj.project_is_deleted=0 AND up.user_id='.$uid.' ORDER BY project_name ASC');
		$monthlySiteResourceLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"monthly site resource report", "is_company"=>0), '*');
		$data['monthlySiteResourceLastWork'] = $monthlySiteResourceLastWork[0];

		$annualSiteResourceLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"annual site resource report", "is_company"=>0), '*');
		$data['annualSiteResourceLastWork'] = $annualSiteResourceLastWork[0];

		$siteDiaryLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"site diary report", "is_company"=>0), '*');
		$data['siteDiaryLastWork'] = $siteDiaryLastWork[0];				

		$safetyWalkMultipleReportLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"safety walk multiple report", "is_company"=>0), '*');
		$data['sWMRLastWork'] = $safetyWalkMultipleReportLastWork[0];
		if($safetyWalkMultipleReportLastWork!=false){
			$SWMRLocations = $this->adminmodel->getDetails('project_locations',array("project_id"=>$safetyWalkMultipleReportLastWork[0]['project_id'], "location_parent_id"=>'0', "location_is_deleted"=>0), 'location_id, location_title');
			$data['SWMRLocations'] = $SWMRLocations;
		}
		
		$safetyWalkSummaryReportLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"safety walk summary report", "is_company"=>0), '*');
		$data['sWSRLastWork'] = $safetyWalkSummaryReportLastWork[0];				
		if($safetyWalkSummaryReportLastWork!=false){
			$SWSRLocations = $this->adminmodel->getDetails('project_locations',array("project_id"=>$safetyWalkSummaryReportLastWork[0]['project_id'], "location_parent_id"=>'0', "location_is_deleted"=>0), 'location_id, location_title');
			$data['SWSRLocations'] = $SWSRLocations;
		}
		
		$safetyWalkSubContractorsSummaryReportLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"safety walk subcontractors summary report", "is_company"=>0), '*');
		$data['swSubConSRLastWork'] = $safetyWalkSubContractorsSummaryReportLastWork[0];				
		if($safetyWalkSubContractorsSummaryReportLastWork!=false){
			$swSubConSRLocations = $this->adminmodel->getDetails('project_locations',array("project_id"=>$safetyWalkSubContractorsSummaryReportLastWork[0]['project_id'], "location_parent_id"=>'0', "location_is_deleted"=>0), 'location_id, location_title');
			$data['swSubConSRLocations'] = $swSubConSRLocations;
		}
		
		$incSummaryRegLastWork = $this->adminmodel->getDetails('remember_last_work',array("user_id" => $this->session->userdata('uid'), "section_name"=>"Incident summary register report", "is_company"=>0), '*');
		$data['injSumRegLastWork'] = $incSummaryRegLastWork[0];				
						
		$this->load->view('reports',$data);
	}
	
# Report Genrate by Ajax
	public function genrateReport(){
		//$this->loginExists();
		$data['publicUrl']=$this->config->item('public_url');
		
/*		$pid = $this->input->post("projectId");	*/
		$uid = $this->session->userdata('uid');
		$where = "uproj.user_id='".$uid."'";
		$searchKey = $this->input->post("searchKey");
		$data['quarter'] = $this->input->post("quarter");
		$data['year'] = $this->input->post("year");

//		if($pid!=0){ $where.= " and iirpt.project_id='".$pid."'"; }
//		if(!empty($searchKey)){ $where.= " and iirpt.description_of_incident like '%".$searchKey."%'"; }
		if(!empty($searchKey)){ $where.= ' and iirpt.details like "%'.$searchKey.'%"'; }		
		if(!empty($data['quarter'])){
			$d = explode(',', $data['quarter']);
			$data['jobMonths'] = $d;
			$from = $data['year']."-".$d[0]."-01";
			$where.= " and iirpt.created_date >='".$from."'"; 
			$to = $data['year']."-".$d[2]."-32";
			$where.= " and iirpt.created_date <'".$to."'";
		}
		
		//$query = "select * from incident_investigation_report where ".$where;
	# Count Minor & Mager in project
	$jobList = "select uproj.project_id as jobNo, uproj.project_name as jobName from user_projects as uproj left join incident_investigation_report as iirpt on uproj.project_id = iirpt.project_id where ".$where." group by iirpt.project_id ORDER BY uproj.`project_created` ASC";

$data['jobList'] = $this->adminmodel->getDetails("",'','',$jobList);
$jobDetails = array();
if(isset($data['jobList']) && !empty($data['jobList'])){
	foreach($data['jobList'] as $job){
	$projects = "select count(iirpt.classification) as count, iirpt.incident_investigation_report_id as rptID, uproj.project_id as jobNo, uproj.project_name as jobName, iirpt.injury_type, iirpt.classification, iirpt.severity, iirpt.created_date from user_projects as uproj left join incident_investigation_report as iirpt on uproj.project_id = iirpt.project_id where ".$where."  and uproj.project_id=".$job['jobNo']." group by iirpt.`created_date`, iirpt.classification, iirpt.project_id ORDER BY iirpt.`created_date` ASC, uproj.project_name asc, iirpt.classification asc";

		$data['jobDetails'][$job['jobNo']]= $this->adminmodel->getDetails("",'','',$projects);
	}
}


		$projectDetails = "select uproj.project_id, uproj.project_name, iirpt.created_date, iirpt.report_contact_company, iirpt.details, iirpt.classification, iirpt.injury_type, iirpt.trades from user_projects as uproj left join incident_investigation_report as iirpt on uproj.project_id = iirpt.project_id where ".$where." and iirpt.injury_type!='' and iirpt.classification!='' ORDER BY iirpt.created_date ASC";
		$data['projectDetails'] = $this->adminmodel->getDetails("",'','',$projectDetails);

//	$data = $this->load->view('htmlReport',$data);
	
echo	$data = $this->load->view('htmlReport',$data,true);

		$data = str_ireplace('<img onClick="printDiv();" src="../swidpublice/swidTheme/images/print_btn.png" style="float:left; margin-right:8%;" />
<a href="'.base_url().'manager/genratePDF/" ><img onClick="downloadPDF();"src="../swidpublice/swidTheme/images/download_btn.png" style="float:right;" /></a>', "",$data);
echo $path=file_exists(curFilePath."images/logo.png")?curntPath:defaltPath;
		$data = str_ireplace($path."images/", "../swidpublice/".SUBDOMAIN."/images/",$data);
		$data = str_ireplace('<img onClick="printDiv();" src="images/print_btn.png" style="float:left; margin-right:8%;" />
<a href="'.base_url().'manager/genratePDF/" ><img onClick="downloadPDF();"src="images/download_btn.png" style="float:right;" /></a>', "",$data);



		$f=fopen('dompdf/rpt.html','w');
		fwrite($f,$data);
		fclose($f);
		//$this->genratePDF('rpt.html');
/*	die	*/	
	}
	
# Generate PDF fileys
	public function genratePDF($input_file='rpt.html'){
	require_once("dompdf/dompdf_config.inc.php");
	//$base_path=DOMPDF_CHROOT;
	$base_path = "dompdf/";
	global $_dompdf_show_warnings, $_dompdf_debug, $_DOMPDF_DEBUG_TYPES;
	$sapi = php_sapi_name();
	$options = array();

switch ( $sapi ) {
 default:

  if ( isset($input_file) )
    $file = rawurldecode($input_file);
  else
    throw new DOMPDF_Exception("An input file is required (i.e. input_file _GET variable).");
  
  if ( isset($_GET["paper"]) )
    $paper = rawurldecode($_GET["paper"]);
  else
    $paper = DOMPDF_DEFAULT_PAPER_SIZE;
  
  if ( isset($_GET["orientation"]) )
    $orientation = rawurldecode($_GET["orientation"]);
  else
   // $orientation = "portrait";
    $orientation = "landscape";

  if ( isset($base_path) ) {
    $base_path = rawurldecode($base_path);
    $file = $base_path . $file; # Set the input file
  }  

  if ( isset($_GET["options"]) ) {
    $options = $_GET["options"];
  }
  
  $file_parts = explode_url($file);


  /* Check to see if the input file is local and, if so, that the base path falls within that specified by DOMDPF_CHROOT */
  if(($file_parts['protocol'] == '' || $file_parts['protocol'] === 'file://')) {
    $file = realpath($file);

    if ( strpos($file, DOMPDF_CHROOT) !== 0 ) {
      throw new DOMPDF_Exception("Permission denied on $file.");
    }
  }
  
  $outfile = "dompdf_out.pdf"; # Don't allow them to set the output file
  $save_file = true; # Don't save the file
  
  break;
}



$dompdf = new DOMPDF();

if ( $file === "-" ) {
  $str = "";
  while ( !feof(STDIN) )
    $str .= fread(STDIN, 4096);

  $dompdf->load_html($str);

} else
  $dompdf->load_html_file($file);

if ( isset($base_path) ) {
  $dompdf->set_base_path($base_path);
}

$dompdf->set_paper($paper, $orientation);

$dompdf->render();

if ( $_dompdf_show_warnings ) {
  global $_dompdf_warnings;
  foreach ($_dompdf_warnings as $msg)
    echo $msg . "\n";
  echo $dompdf->get_canvas()->get_cpdf()->messages;
  flush();
}

if ( $save_file ) {

//   if ( !is_writable($outfile) )
//     throw new DOMPDF_Exception("'$outfile' is not writable.");
  if ( strtolower(DOMPDF_PDF_BACKEND) === "gd" )
    $outfile = str_replace(".pdf", ".png", $outfile);

  list($proto, $host, $path, $file) = explode_url($outfile);
  if ( $proto != "" ) // i.e. not file://
    $outfile = $file; // just save it locally, FIXME? could save it like wget: ./host/basepath/file

  $outfile = realpath(dirname($outfile)) . DIRECTORY_SEPARATOR . basename($outfile);

  if ( strpos($outfile, DOMPDF_CHROOT) !== 0 )
    throw new DOMPDF_Exception("Permission denied.");

  file_put_contents($outfile, $dompdf->output( array("compress" => 0) ));
 // exit(0);
}

	header('Content-disposition: attachment; filename=Incident_Investigation_Report.pdf');
header('Content-type: application/pdf');
readfile('dompdf_out.pdf');
exit;
if ( !headers_sent() ) {
  $dompdf->stream($outfile, $options);
}
	}


}