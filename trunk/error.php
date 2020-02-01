<?php
	include('_controller.php');
	function sitm() {
		global $BF;
 ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="home_content">
			<tr>
				<td width="100%">
					<form id='idForm' name='idForm' method='post' action=''>
					<div style="text-align:center; font-size:14px;"><strong>An Error as occurred! This is usually due to missing or incomplete information.</strong></div>
<?
if(isset($_SESSION['chrErrorMsg'])) {
?>
					<div style="text-align:center; font-size:12px; padding-top:20px;">
						<strong>Error Details:</strong>
						<div class='ErrorMessage'><?=$_SESSION['chrErrorMsg']?></div>
					</div>
<?
	unset($_SESSION['chrErrorMsg']);
}
?>
					<div style="text-align:center; padding-top:20px;">
							<?=form_button(array('type'=>'button','name'=>'Back','value'=>'Back','extra'=>'onclick="javascript: history.go(-1);"'))?>&nbsp;&nbsp;&nbsp;
							<?=form_button(array('type'=>'button','name'=>'Home','value'=>'Home','extra'=>'onclick="javascript:location.href=\''.$BF.'index.php\'";'))?>
							</div>
					</form>
				</td>
			</tr>
		</table>
		
		
<?	} ?>