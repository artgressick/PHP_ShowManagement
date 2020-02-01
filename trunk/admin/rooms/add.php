<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;
?>
	<div class='innerbody'>
		<form enctype="multipart/form-data" action="" method="post" id="idForm" onsubmit="return error_check()">

			<div class="colHeader2">Room Information</div>

			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
				
						<?=form_text(array('caption'=>'Room Name','required'=>'true','name'=>'room_name','size'=>'30','maxlength'=>'200'))?>

						<?=form_text(array('caption'=>'Room Number','required'=>'true','name'=>'room_number','size'=>'30','maxlength'=>'200'))?>

<?					
						$building = db_query("SELECT id, building_name as name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Getting Buildings");
?>
						<?=form_select($building,array('caption'=>'Building','required'=>'true','name'=>'building_id'))?>

<?					
						$platforms = db_query("SELECT id, platform_name as name FROM platforms ORDER BY id","Getting platforms");
?>
						<?=form_select($platforms,array('caption'=>'Platform','required'=>'Required) (Indicates what types of computers are in this room','name'=>'platform_id'))?>

						<?=form_checkbox(array('type'=>'radio','caption'=>'Has Internet Access','title'=>'No','name'=>'internet_access','id'=>'internet_access0','value'=>'0','checked'=>'true'))?>&nbsp;&nbsp;&nbsp;

<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'internet_access','id'=>'internet_access1','value'=>'1','checked'=>'false'))?>

						
						<?=form_text(array('caption'=>'Capacity','name'=>'capacity','size'=>'10','maxlength'=>'10'))?>
						
						<?=form_text(array('caption'=>'Square Footage','name'=>'square_feet','size'=>'10','maxlength'=>'10'))?>
						
						<?=form_text(array('caption'=>'Dimensions','name'=>'dimensions','size'=>'15','maxlength'=>'20'))?>
						
						<?=form_text(array('caption'=>'Public Location','name'=>'public_location','size'=>'30','maxlength'=>'200'))?>
						
						<?=form_text(array('caption'=>'Default Setup','name'=>'default_setup','size'=>'30','maxlength'=>'255'))?>
						
						<?=form_text(array('caption'=>'Category','name'=>'category','size'=>'30','maxlength'=>'255'))?>

						<div class="colHeader" style="width:100%;">Diagrams</div>
						<table id='Files' cellspacing="0" cellpadding="0" style='margin-top: 10px;'>
							<tbody id="Filestbody">
							<tr>
								<td>Diagram 1:&nbsp;&nbsp;</td>
								<td id='Filesfile1'><input type='file' name='chrFilesFile1' id='chrFilesFile1' /></td>
							</tr>
							</tbody>
						</table>
						<div style='padding: 5px 10px;'><input type='button' onclick='javascript:newOption(2,"Files");' value='Add Another' /></div>
						<input type='hidden' name='intFiles' id='intFiles' value='1' />
						
						
					</td>
					<td class="tcgutter"></td>
					<td class="tcright">

						<table cellpadding="0" cellspacing="0">
							<tr>
								<td><?=form_text(array('caption'=>'Move-in Date','name'=>'move_in-date','size'=>'10','maxlength'=>'100'))?></td>
								<td width="5">&nbsp;</td>
								<td><?=form_text(array('caption'=>'Move-in Time','name'=>'move_in-time','size'=>'10','maxlength'=>'100'))?></td>
							</tr>
							<tr>
								<td><?=form_text(array('caption'=>'Move-out Date','name'=>'move_out-date','size'=>'10','maxlength'=>'100'))?></td>
								<td width="5">&nbsp;</td>
								<td><?=form_text(array('caption'=>'Move-out Time','name'=>'move_out-time','size'=>'10','maxlength'=>'100'))?></td>
							</tr>
						</table>

						<?=form_textarea(array('caption'=>'Room Description','name'=>'description','cols'=>'60','rows'=>'10'))?>

						<div class="colHeader">Notes</div>

						<?=form_textarea(array('caption'=>'Standard Room Set Notes (displays on order page under room set)','name'=>'notes','cols'=>'60','rows'=>'10'))?>

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