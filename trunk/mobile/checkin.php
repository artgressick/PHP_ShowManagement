<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;

?>
		<div style="text-align:center;">
			<div class="colHeader" style="margin-top:10px;">Tracking Number</div>
			<div><input type="text" name="tracking_number" id="tracking_number" size="40" maxlength="200" onchange="submittracking();" /></div>
			
			<div style="margin-top:20px;"><a class="blue button" href="#">Submit</a></div>
		</div>
<?
	}
?>