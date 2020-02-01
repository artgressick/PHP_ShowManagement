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
				
						<div class="colHeader">Building Information</div>

						<?=form_text(array('caption'=>'Building Name','required'=>'true','name'=>'building_name','size'=>'30','maxlength'=>'150'))?>
						<?=form_text(array('caption'=>'Access Date','name'=>'access','size'=>'30','maxlength'=>'100'))?>
						<?=form_text(array('caption'=>'Departure Date','name'=>'depart','size'=>'30','maxlength'=>'100'))?>

					</td>
					<td class="tcgutter"></td>
					<td class="tcright">

						<div class="colHeader">Notes</div>
						
						<?=form_textarea(array('caption'=>'Delivery Information','name'=>'delivery_info','cols'=>'60','rows'=>'10'))?>

						<?=form_textarea(array('caption'=>'Notes','name'=>'notes','cols'=>'60','rows'=>'10'))?>

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