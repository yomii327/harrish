<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
	<?php include'data-table.php';?>
	<!-- Ajax Post -->
	</head>
	<body id="dt_example">
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Task</legend>
			<div class="demo_jui" style="width:99%" >
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="task_server" width="100%" style="color:#000000;">
					<thead>
						<tr>
							<th width="85%" nowrap="nowrap">Task</th>
							<!-- <th width="35%" nowrap="nowrap">Comment</th> -->
							<th width="15%">Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5" class="dataTables_empty">Loading data from server</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="spacer"></div>
			<br clear="all" />
		</fieldset>
	</body>
</html>