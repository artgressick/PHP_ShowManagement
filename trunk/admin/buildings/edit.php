<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:800px;'>
				<tr>
					<td class="tcleft">
				
						<div class="colHeader">Building Information</div>

						<?=form_text(array('caption'=>'Building Name','required'=>'true','name'=>'building_name','size'=>'30','maxlength'=>'150','value'=>$info['building_name']))?>
						<?=form_text(array('caption'=>'Access Date','name'=>'access','size'=>'30','maxlength'=>'100','value'=>$info['access']))?>
						<?=form_text(array('caption'=>'Departure Date','name'=>'depart','size'=>'30','maxlength'=>'100','value'=>$info['depart']))?>

					</td>
					<td class="tcgutter"></td>
					<td class="tcright">

						<div class="colHeader">Notes</div>
						
						<?=form_textarea(array('caption'=>'Delivery Information','name'=>'delivery_info','cols'=>'60','rows'=>'10','value'=>$info['delivery_info']))?>

						<?=form_textarea(array('caption'=>'Notes','name'=>'notes','cols'=>'60','rows'=>'10','value'=>$info['notes']))?>

					</td>
				</tr>
			</table>
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Update Information'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
			</div>
		</form>
	</div>

<?
	}
?>