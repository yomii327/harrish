<?php session_start();

$builder_id = $_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["antiqueID"])){
}

if(isset($_REQUEST["name"])){?>
	<fieldset class="roundCorner" style="color:#000000;">
		<legend>Add Drawing Register</legend>
		<div id="searchDraw" style="border:1px solid #818080;padding:5px;">
			<table width="80%" border="0" align="center">
				<tr>
					<td style="padding-left:10px;" >Attribute 1</td>
					<td style="padding-left:10px;">Attribute 2</td>
					<td style="padding-left:10px;">Search Keyword</td>
					<td style="padding-left:10px;">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<select name="drawingattribute1" id="drawingattribute1Search" class="select_box" style="width: 165px;
	background-image: url(images/input_160.png);margin-left:0px;"  />
							<option value="">Select</option>
						<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete & PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
						if($_SESSION['userRole'] == 'Architect'){
							$attribute1Arr = array('Architectural');
						}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
							$attribute1Arr = array('Structure');
						}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
							$attribute1Arr = array('Services');
						}
						if($_SESSION['userRole'] == 'Lighting Consultant')	$attribute1Arr = array('Lighting');
						if($_SESSION['userRole'] == 'Tenancy Fitout')	$attribute1Arr = array('Tenancy Fitout');
						if($_SESSION['userRole'] == 'Penthouse Architecture')	$attribute1Arr = array('Penthouse Architecture');
						if($_SESSION['userRole'] == 'Landscaping')	$attribute1Arr = array('Landscaping');
						
						for($i=0;$i<sizeof($attribute1Arr);$i++){?>
							<option value="<?=$attribute1Arr[$i]?>" <? if($attribute1Arr[$i] == $_REQUEST['attributeVal'])echo 'selected="selected"';?> ><?=$attribute1Arr[$i]?></option>
					<?php }?>
						</select>
					</td>
					<td>
						<select name="drawingattribute2" id="drawingattribute2Search" class="select_box" style="width: 120px;
	background-image: url(images/input_120.png);margin-left:0px;"  />
							<option value="">Select</option>
						</select>
					</td>
					<td>
						<input type="text" name="searchKeyword" id="searchKeywordSearch" class="input_small" style="width: 150px;
	background-image: url(images/input_160.png);" />
					</td>
					<td>
						<img onclick="searchDrawSearch();" style="cursor:pointer; float:right;" src="images/search_draw_reg.png" alt="search" /> 
						<img onclick="resetSearchDynamic();" style="cursor:pointer; float:right;margin:0 10px 0 -30px;" src="images/reset_drw_search.png" title="Reset filter" align=">top"  />
						<input type="hidden" id="hiddenNewDrawingID" value="" />
						<input type="hidden" id="hiddenShowDivID" value="" />
					</td>
				</tr>
			</table>
		</div>
		<strong>Selected Drawing Name:</strong> <div id="selectedDocName" style=""></div>
		<div id="searchResultBox" style="margin-top:15px;display:none;"></div>
	</fieldset>
<?php }?>
