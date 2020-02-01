<?php
	include('_controller.php');

	function sitm() {
		global $BF;
		$last_update = db_query("SELECT last_import_at FROM shows WHERE id=1","Get Last Update",1);
?>
	
	<div style="text-align:center;">Last Import was done on: <?=pretty_datetime($last_update['last_import_at'])?>.</div>
	<div style="text-align:center;"><input type="button" name="start" id="start_btn" value="Start Import" onclick="start_import();" /></div>			
			
	<iframe class="innerbody" id="log_window" src="" height="300px" style="margin-top:10px;">
		<p>Your browser does not support iframes.</p>
	</iframe>
			
					
					
<?
	}
?>