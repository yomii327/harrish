<?php include'data-table.php'; ?>
<style>
	.content_container { width: 81% !important; }
	table#projTable { width: 100% !important; }
</style>
<div id="container">
    <div class="content_hd1 page-main-heading" style="background-image:url(images/projects_hd.png);"> Projects</div>
    <br clear="all" />
    <?php if($_SESSION['web_add_project'] == 1){?>
    <!-- <a href="?sect=add_project"><div class="add_new" style="margin:0 auto;"></div></a> -->
    <a alt="add_new" style="cursor:pointer;float:right;margin-bottom:5px;" href="?sect=add_project" class="green_small">Add New</a>
    <?php }?>
    <div class="demo_jui" style="margin-top:-10px;">
        <?php if(isset($_SESSION['remove_project'])) { ?>
        <div class="success_r" style="width:250px;margin-left:345px;">
            <p><?php echo $_SESSION['remove_project'];?></p>
        </div>
        <?php unset($_SESSION['remove_project']); 
            } ?>
        <?php if(isset($_SESSION['archieve_update'])) { ?>
        <div class="success_r" style="width:250px;margin-left:345px;">
            <p><?php echo $_SESSION['archieve_update'];?></p>
        </div>
        <?php unset($_SESSION['archieve_update']); 
            } ?>
        <?php
            $builder_id=$_SESSION['ww_builder_id'];
            
            $q = "SELECT
            		up.*,
            		up.is_deleted as archieve,
            		p.allow_sync,
            		p.project_is_synced,
            		p.company_name as companyName
            	FROM
            		user_projects as up,
            		user as u,
            		projects as p
            	WHERE ";
                           if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1){
                             $q.= "u.user_id = $builder_id AND ";
                           }  
            		
            	$q.="up.user_id = u.user_id AND
            		p.project_id = up.project_id AND
            		p.is_deleted IN (0, 2) AND
            		up.is_deleted IN (0, 2) AND
            		u.is_deleted = 0
            	GROUP BY 
            		up.project_id";
               #echo $q; die;
               ?>      
        <label style=""><input type="checkbox" id="chk_all_prpjects" value="check" checked /> Show Only Active Projects</label>
        <?php 
            if(isset($_SESSION['add_project'])) { ?> 
        <div align="center">Project added successfully</div>
        <?php unset($_SESSION['add_project']); } ?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="projTable">
            <thead>
                <tr>
                    <th nowrap="nowrap">Head by</th>
                    <th>Project Name</th>
                    <th>Project Type</th>
                    <th>Company</th>
                    <th>Address</th>
                    <th>Suburb</th>
                    <th>State</th>
                    <th>Postcode</th>
                    <th>Country</th>
                    <th>Archive</th>
                    <th>Email on Sync</th>
                    <th>Sync Project To Ipad</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php 	//wordwrap($f['description'],60,"<br />\n",true);
                    $r=mysql_query($q);
                    while($f=mysql_fetch_assoc($r)){
                    	//if($f['sb_id']==$builder_id)$head_by=stripslashes($f['user_fullname']);else $head_by='Own';
                    	if($f['archieve']==2){
                    		$f['archieve']="Yes";
                    	}else{
                    		$f['archieve']="No";
                    	}
                    	$head_by='own';
                    	echo "<tr class='gradeA'>
                    		<td>".$head_by."</td>
                    		<td>".wordwrap(stripslashes($f['project_name']),40,"<br />\n",true)."</td>
                    		<td>".stripslashes($f['project_type'])."</td>
                    		<td>".stripslashes($f['companyName'])."</td>
                    		<td>".wordwrap(stripslashes($f['project_address_line1']),40,"<br />\n",true)."</td>
                    		<td>".stripslashes($f['project_suburb'])."</td>
                    		<td>".stripslashes($f['project_state'])."</td>
                    		<td>".stripslashes($f['project_postcode'])."</td>
                    		<td>".stripslashes($f['project_country'])."</td>
                    		<td>".stripslashes($f['archieve'])."</td>
                    		<td>".stripslashes($f['allow_sync'])."</td>
                    		<td>".stripslashes($f['project_is_synced'])."</td>
                    		<td align='center'>
                    			<a  title='Click to see project details' href='?sect=add_project_detail&id=".base64_encode($f['project_id'])."&hb=".base64_encode($builder_id)."' style='float:left;'><img src='images/edit.png' border='none' /></a>
                    		</td>
                    	    </tr>";
                    } ?>
            </tbody>
        </table>
    </div>
    <div class="spacer"></div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
    	oTable = $('#projTable').dataTable({
    		"bJQueryUI": true,
    		"sPaginationType": "full_numbers",
    		"bStateSave": true,
    		"sCookiePrefix": "projTableCookie",
    	});
    	$('#chk_all_prpjects').on('change', function(){
    		if($(this).is(":checked")) { var all_projects = 'check' }else{ var all_projects = 'uncheck' }
    		showProgress();
    		$.ajax({
    		    url: "project_table_ajax.php",
    		    data: {"all_projects": all_projects},
    		    //cache: false,
    		    type: "POST",
    		    success: function(response) {
    		    	$('#projTable').dataTable().fnDestroy();
    		    	$('#projTable').html(response);
    				oTable = $('#projTable').dataTable({
    					"bJQueryUI": true,
    					"sPaginationType": "full_numbers",
    					"bStateSave": true,
    					"sCookiePrefix": "projTableCookie",
    				});
    				hideProgress();
    		    },
    		    error: function(xhr) {
    
    		    }
    		});
    		
        });
        $('#chk_all_prpjects').trigger('change');
    } );
</script>