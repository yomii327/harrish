<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Feedback and Support</title>

<style type="text/css">
table.gridtable {
	border-width: 1px;
	border-color: #FFF;
	border-collapse: collapse;
}
table.gridtable td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	/*border-color: #0369AB;*/
	border-color: #FFF;
}
</style>
</head>

<body>
<div name="feedbackFrm" id="feedbackFrm" style="color:#000;width:350px;">
<table  style="color:#000;" class="gridtable"> 
  <tr>
    <td colspan="2" align="center"><h3 style="color:#0369AB;">Feedback &amp; Support</h3></td>
   
  </tr>
  <tr>
    <td>Type</td>
    <td>
		<input type="radio" value="Feedback" name="fs" id="fs1" /> &nbsp;<label for="fs1">Feedback</label><br/>
    	<input type="radio" value="Support" name="fs" id="fs2" checked="checked" />&nbsp;<label for="fs2">Support</label>
     </td>
  </tr>
  <tr height="50px;">
    <td >Description</td>
    <td><textarea name="desc" id="desc" ></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="image" name="sub" id="sub" src="images/submit.png" onclick="return descchek();" /></td>
   
  </tr>
</table>
</div>
</body>
</html>