<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;

?>
	<div class='innerbody'>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
					<div class="colHeader2">Tracking Number</div>
					<div><input type="text" name="tracking_number" id="tracking_number" size="40" maxlength="200" onchange="submittracking();" /></div>
				</td>
			</tr>
		</table>
		<div class='FormButtons' style="margin-top:20px;">
			<?=form_button(array('type'=>'submit','value'=>'Back to List','extra'=>'onclick="location.href=\'index.php\';"'))?>
		</div>
	</div>

<?
	}
?>