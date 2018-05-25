<?php
//**************************************
//     Page load dropdown results     //
//**************************************
function getTierOne()
{
	$query='SELECT DISTINCT tier_one FROM three_drops';
	echo $query; die;
	$result = mysql_query($query)or die(mysql_error());

	  while($tier = mysql_fetch_array( $result )) 
  
		{
		   echo '<option value="'.$tier['tier_one'].'">'.$tier['tier_one'].'</option>';
		}

}

//**************************************
//     First selection results     //
//**************************************
if(isset($_GET['func'])) { 
   drop_1($_GET['drop_var']); 
}

function drop_1($drop_var)
{  
    include_once('db.php');
	$result = mysql_query("SELECT DISTINCT tier_two FROM three_drops WHERE tier_one='$drop_var'") 
	or die(mysql_error());
	
	echo '<select name="drop_2" id="drop_2">
	      <option value=" " disabled="disabled" selected="selected">Choose one</option>';

		   while($drop_2 = mysql_fetch_array( $result )) 
			{
			  echo '<option value="'.$drop_2['tier_two'].'">'.$drop_2['tier_two'].'</option>';
			}
	
	echo '</select>';
	echo "<script type=\"text/javascript\">
$('#wait_2').hide();
	$('#drop_2').change(function(){
	  $('#wait_2').show();
	  $('#result_2').hide();
      $.get(\"func.php\", {
		func: \"drop_2\",
		drop_var: $('#drop_2').val()
      }, function(response){
        $('#result_2').fadeOut();
        setTimeout(\"finishAjax_tier_three('result_2', '\"+escape(response)+\"')\", 400);
      });
    	return false;
	});
</script>";
}


//**************************************
//     Second selection results     //
//**************************************
if(isset($_GET['func'])) { 
   drop_2($_GET['drop_var']); 
}

function drop_2($drop_var)
{  
    include_once('db.php');
	$result = mysql_query("SELECT * FROM three_drops WHERE tier_two='$drop_var'") 
	or die(mysql_error());
	
	echo '<select name="drop_3" id="drop_3">
	      <option value=" " disabled="disabled" selected="selected">Choose one</option>';

		   while($drop_3 = mysql_fetch_array( $result )) 
			{
			  echo '<option value="'.$drop_3['tier_three'].'">'.$drop_3['tier_three'].'</option>';
			}
	
	echo '</select> ';
    echo '<input type="submit" name="submit" value="Submit" />';
}
?>