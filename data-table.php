<style type="text/css" title="currentStyle">
	@import "datatable/media/css/demo_page.css";
	@import "datatable/media/css/demo_table_jui.css";
	@import "datatable/examples_support/themes/smoothness/jquery-ui-1.8.16.custom.css";
</style>
<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		oTable = $('#example').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bStateSave": true
		});
	} );
	
	$(document).ready(function() {
		oTable = $('#example_1').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bStateSave": true
		});
	} );
	
	$(document).ready(function() {
		oTable = $('#example_2').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bStateSave": true
		});
	} );
</script>