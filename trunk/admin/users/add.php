<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
				
						<div class="colHeader">Personal Information</div>

						<?=form_text(array('caption'=>'First Name','required'=>'true','name'=>'first_name','size'=>'30','maxlength'=>'100'))?>
						<?=form_text(array('caption'=>'Last Name','required'=>'true','name'=>'last_name','size'=>'30','maxlength'=>'100'))?>
						<?=form_text(array('caption'=>'Email Address','required'=>'true','name'=>'email','size'=>'30','maxlength'=>'150'))?>
						
						<?=form_text(array('caption'=>'Cell Number','name'=>'cellnumber','size'=>'20','maxlength'=>'30'))?>
<?						
						$q = "SELECT id, mobile_carrier AS name FROM mobile_carriers ORDER BY mobile_carrier";
						$mobilecarriers = db_query($q,"getting mobile carriers");
?>
						<?=form_select($mobilecarriers,array('caption'=>'Mobile Carrier','name'=>'mobile_carrier_id'))?>

						<div class="colHeader">Password</div>
						
						<?=form_text(array('caption'=>'Password','type'=>'password','required'=>'true','name'=>'crypted_password','size'=>'30','maxlength'=>'100'))?>
						<?=form_text(array('caption'=>'Confirm Password','type'=>'password','required'=>'true','name'=>'crypted_password2','size'=>'30','maxlength'=>'100'))?>


					</td>
					<td class="tcgutter"></td>
					<td class="tcright">

						<div class="colHeader">Account Options</div>
<?					
						$groups = db_query("SELECT id, group_name as name FROM groups ORDER BY id","Getting Groups");
?>
						<?=form_select($groups,array('caption'=>'Access Group','required'=>'true','name'=>'group_id'))?>
						
						<?=form_checkbox(array('type'=>'radio','caption'=>'Account Enabled','title'=>'No','name'=>'enabled','id'=>'enabled0','value'=>'0','required'=>'true','checked'=>'false'))?>&nbsp;&nbsp;&nbsp;

<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'enabled','id'=>'enabled1','value'=>'1','checked'=>'true'))?>
						
	
					</td>
				</tr>
			</table>
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Add Another','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'add.php\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'submit','value'=>'Add and Continue','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'index.php\';"'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'moveTo'))?>
			</div>
		</form>
	</div>

<?
	}
?>