<?php session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
if(isset($_POST['name'])){
	$attributeArr1 = array();
	$attributeArr2 = array();
	$attributeData = $obj->selQRYMultiple('dr_attribute_1, dr_attribute_2', 'user_projects', 'is_deleted = 0 AND project_id = '.$_POST['projectid'].' AND user_id = '.$_POST['userid']);
	$attributeArr1 = explode(",", $attributeData[0]['dr_attribute_1']);
	$attributeArr2 = explode(",", $attributeData[0]['dr_attribute_2']);
#print_r($attributeArr1);print_r($attributeArr2);

?>
	<form id="drawingRegisterForm" name="drawingRegisterForm">
		<span id="span_general">
			<ul class="telefilms"><!-- Location Level One -->
				<li id="li_general">
					<input type="checkbox" name="attr1_General" id="checkbox_general"  value="1" onclick="toggleCheckDC(this, 'li_general');" 
					<? if(in_array('"General"', $attributeArr1)){ echo 'checked="checked"'; }?> />
					<span class="jtree-button demo1" id="span_general">General</span>
					<ul class="telefilms"><!-- Location Level One -->
						<li id="li_reports">
							<input type="checkbox" name="attr2_Reports" id="checkbox_reports"  value="1" onclick="parengCheckedDC(this, 'checkbox_general');"
							<? if(in_array('"Reports"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_reports">Reports</span>
						</li>
						<li id="li_3d_images">
							<input type="checkbox" name="attr2_3D images" id="checkbox_3d_images"  value="1" onclick="parengCheckedDC(this, 'checkbox_general');"
							<? if(in_array('"3D images"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_3d_images">3D images</span>
						</li>
						<li id="li_marketing">
							<input type="checkbox" name="attr2_Marketing" id="checkbox_marketing"  value="1" onclick="parengCheckedDC(this, 'checkbox_general');"
							<? if(in_array('"Marketing"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_marketing">Marketing</span>
						</li>
						<li id="li_brief_and_overview">
							<input type="checkbox" name="attr2_Brief and overview" id="checkbox_brief_and_overview"  value="1" onclick="parengCheckedDC(this, 'checkbox_general');"
							<? if(in_array('"Brief and overview"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_brief_and_overview">Brief and overview</span>
						</li>
					</ul>
				</li>
				<li id="li_architectural">
					<input type="checkbox" name="attr1_Architectural" id="checkbox_architectural"  value="1" onclick="toggleCheckDC(this, 'li_architectural');"
					<? if(in_array('"Architectural"', $attributeArr1)){ echo 'checked="checked"'; }?> />
					<span class="jtree-button demo1" id="span_architectural">Architectural</span>
					<ul class="telefilms"><!-- Location Level One -->
						<li id="li_plans">
							<input type="checkbox" name="attr2_Plans" id="checkbox_plans"  value="1" onclick="parengCheckedDC(this, 'checkbox_architectural');"
							<? if(in_array('"Plans"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_plans">Plans</span>
						</li>
						<li id="li_elevations">
							<input type="checkbox" name="attr2_Elevations" id="checkbox_elevations"  value="1" onclick="parengCheckedDC(this, 'checkbox_architectural');"
							<? if(in_array('"Elevations"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_elevations">Elevations</span>
						</li>
						<li id="li_sections">
							<input type="checkbox" name="attr2_Sections" id="checkbox_sections"  value="1" onclick="parengCheckedDC(this, 'checkbox_architectural');"
							<? if(in_array('"Sections"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_sections">Sections</span>
						</li>
						<li id="li_RCPs">
							<input type="checkbox" name="attr2_RCP's" id="checkbox_RCPs"  value="1" onclick="parengCheckedDC(this, 'checkbox_architectural');"
							<? if(in_array('"RCP\'s"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_RCPs">RCP's</span>
						</li>
						<li id="li_apartment_plans">
							<input type="checkbox" name="attr2_Apartment Plans" id="checkbox_apartment_plans"  value="1" onclick="parengCheckedDC(this, 'checkbox_architectural');"
							<? if(in_array('"Apartment Plans"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_apartment_plans">Apartment Plans</span>
						</li>
						<li id="li_specifications">
							<input type="checkbox" name="attr2_Specifications" id="checkbox_specifications"  value="1" onclick="parengCheckedDC(this, 'checkbox_architectural');"
							<? if(in_array('"Specifications"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_specifications">Specifications</span>
						</li>
					</ul>
				</li>
				<li id="li_structure">
					<input type="checkbox" name="attr1_Structure" id="checkbox_structure"  value="1" onclick="toggleCheckDC(this, 'li_structure');"
					<? if(in_array('"Structure"', $attributeArr1)){ echo 'checked="checked"'; }?> />
					<span class="jtree-button demo1" id="span_structure">Structure</span>
				</li>
				<li id="li_services">
					<input type="checkbox" name="attr1_Services" id="checkbox_services"  value="1" onclick="toggleCheckDC(this, 'li_services');"
					<? if(in_array('"Services"', $attributeArr1)){ echo 'checked="checked"'; }?> />
					<span class="jtree-button demo1" id="span_services">Services</span>
					<ul class="telefilms"><!-- Location Level One -->
						<li id="li_mechanical">
							<input type="checkbox" name="attr2_Mechanical" id="checkbox_mechanical"  value="1" onclick="parengCheckedDC(this, 'checkbox_services');"
							<? if(in_array('"Mechanical"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_mechanical">Mechanical</span>
						</li>
						<li id="li_electrical">
							<input type="checkbox" name="attr2_Electrical" id="checkbox_electrical"  value="1" onclick="parengCheckedDC(this, 'checkbox_services');"
							<? if(in_array('"Electrical"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_electrical">Electrical</span>
						</li>
						<li id="li_hydraulic">
							<input type="checkbox" name="attr2_Hydraulic" id="checkbox_hydraulic"  value="1" onclick="parengCheckedDC(this, 'checkbox_services');"
							<? if(in_array('"Hydraulic"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_hydraulic">Hydraulic</span>
						</li>
						<li id="li_fire">
							<input type="checkbox" name="attr2_Fire" id="checkbox_fire"  value="1" onclick="parengCheckedDC(this, 'checkbox_services');"
							<? if(in_array('"Fire"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_fire">Fire</span>
						</li>
						<li id="li_services_specifications">
							<input type="checkbox" name="attr2_Services specifications" id="checkbox_services_specifications"  value="1" onclick="parengCheckedDC(this, 'checkbox_services');"
							<? if(in_array('"Services specifications"', $attributeArr2)){ echo 'checked="checked"'; }?> />
							<span class="jtree-button demo1" id="span_services_specifications">Services specifications</span>
						</li>
					</ul>
				</li>
			</ul>
		</span>
	</form>
	<img src="images/save_min.png" name="span_general" id="span_general" onclick="saveData4drSync()" style="float:right;margin-right:26px;cursor:pointer;"  />
<?php
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Data Updated Successfully !', 'html' => $htmlOutput);
	#echo json_encode($outputArr);
}
if(isset($_GET['uniqueID'])){
	$attribute_one = array();
	$attribute_two = array();
	$updateQRY = "UPDATE user_projects SET ";
	foreach($_POST as $key=>$value){
		if(strpos($key, 'attr1_') !== false){
			$tmpArr = explode("_", $key);
			array_shift($tmpArr);
			$attribute_one[] = '"'.addslashes( implode(' ', $tmpArr) ).'"';
		}else{
			$tmpArr = explode("_", $key);
			array_shift($tmpArr);
			$attribute_two[] = '"'.addslashes( implode(' ', $tmpArr) ).'"';
		}
	}
	$updateQRY = "UPDATE user_projects SET 
						dr_attribute_1 = '".join(",", $attribute_one)."',
						dr_attribute_2 = '".join(",", $attribute_two)."'
					WHERE
						project_id = ".$_GET['projectid']." AND
						user_id = ".$_GET['userid']." AND 
						is_deleted = 0 ";
	mysql_query($updateQRY);
	$status = false;
	if(mysql_affected_rows() > 0){
		$status = true;
	}
	$outputArr = array('status'=> $status, 'msg'=> 'Record updated successfully');
	echo json_encode($outputArr);
}
?>