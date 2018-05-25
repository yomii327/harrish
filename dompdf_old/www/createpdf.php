<?php
require_once("../dompdf_config.inc.php");
if ( isset( $_POST["html"] ) ) {

  if ( get_magic_quotes_gpc() )
    $_POST["html"] = stripslashes($_POST["html"]);
  
  $old_limit = ini_set("memory_limit", "16M");
  
  $dompdf = new DOMPDF();
  $dompdf->load_html($_POST["html"]);
  $dompdf->set_paper($_POST["paper"], $_POST["orientation"]);
  $dompdf->render();

  $dompdf->stream("dompdf_out.pdf");

  exit(0);
}

?>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
	<div>
		<p style="display:none;">
			<select name="paper">
				<?php
foreach ( array_keys(CPDF_Adapter::$PAPER_SIZES) as $size )
  echo "<option ". ($size == "a4" ? "selected " : "" ) . "value=\"$size\">$size</option>\n";
?>
			</select>
			<select name="orientation">
				<option value="portrait">portrait</option>
				<option value="landscape">landscape</option>
			</select>
		</p>
		<textarea name="html" cols="60" rows="20"></textarea>
		<div style="text-align: center; margin-top: 1em;">
			<input type="submit" name="submit" value="submit"/>
		</div>
	</div>
</form>
