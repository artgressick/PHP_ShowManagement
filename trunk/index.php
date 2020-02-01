<?php
	$browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	if ($browser == true)  { 
		header("Location: ".$BF."mobile/");
		die();
	}

	include('_controller.php');

	function sitm() {
		global $BF;
		$landing = db_query("SELECT landing_page FROM shows WHERE id='".$_SESSION['show_id']."'","Get Landing Page",1);
?>
		<div><?=decode($landing['landing_page'])?></div>			
<?
//		print_r($_SERVER);
	}
?>