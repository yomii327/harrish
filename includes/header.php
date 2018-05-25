<?php
	$s = isset($_REQUEST['sect']) ? $_REQUEST['sect'] : '';
	if(isset($_SESSION['ww_is_builder']))
		$f = $_SESSION['ww_is_builder'];
	else
		$f = '';
?>
<?php
	$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$pi = pathinfo($refering_url);
	$u = str_replace('pms.php','',$pi['basename']);
?>
<div id="top">
	<header>
		<div class="container no-padding">
			<div class="row">
				<div class="col-sm-4 no-padding">
					<div class="logo">
						<a href="?sect=b_full_analysis">
							<img src="images/logo.png" border="none" alt="Logo" />
						</a>
					</div>
				</div>
			</div>
		</div>
	</header>

	<!--Navigation-->
	<nav class="navbar-default navigation">
		<div class="container no-padding">
			<div id="nav">
				<ul class="navbar-nav">
					<li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
				</ul>
			</div>
		</div>
	</nav>
</div>
