<?
	include('_controller.php');
	
	function sitm() { 
		global $BF, $time_slots;
?>
	<div class='innerbody'>
		<form enctype="multipart/form-data" action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
				
						<div class="colHeader">Session Information</div>

						<?=form_text(array('caption'=>'Session Name','required'=>'true','name'=>'class_name','size'=>'30','maxlength'=>'255'))?>
						
						<?=form_text(array('caption'=>'Session Number','name'=>'class_number','size'=>'30','maxlength'=>'150'))?>

<?					
						$cats = db_query("SELECT id, sessioncat_name AS name FROM sessioncats WHERE !deleted ORDER BY name","Getting Types");
?>
						<?=form_select($cats,array('caption'=>'Session Category','required'=>'true','name'=>'sessioncat_id'))?>


<?					
						$types = db_query("SELECT id, sessiontype_name AS name FROM sessiontypes WHERE !deleted ORDER BY name","Getting Types");
?>
						<?=form_select($types,array('caption'=>'Session Type','required'=>'true','name'=>'sessiontype_id'))?>

						<?=form_text(array('caption'=>'Speaker','name'=>'speaker','size'=>'30','maxlength'=>'200'))?>

						<?=form_text(array('caption'=>'Audience Size','name'=>'audience_size','size'=>'10','maxlength'=>'10','value'=>'0'))?>
						
						<?=form_text(array('caption'=>'Room Area','name'=>'room_area','size'=>'30','maxlength'=>'200'))?>

						<?=form_textarea(array('caption'=>'Session Description','name'=>'description','cols'=>'60','rows'=>'10'))?>
						
					</td>
					<td class="tcgutter"></td>
					<td class="tcright">
						
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

						<div class="colHeader">Notes</div>
						
						<?=form_textarea(array('caption'=>'Notes','name'=>'notes','cols'=>'60','rows'=>'10'))?>

<?
				$show_data = db_query("SELECT * FROM shows WHERE !deleted AND id=".$_SESSION['show_id'],"Getting Show Information",1);
				if($show_data['session_bill']==1) {
?>
						<div class="colHeader">Billing Information</div>
						
						<?=form_checkbox(array('type'=>'radio','caption'=>'Billing Party.','title'=>'Main Budget','name'=>'bill_other','id'=>'bill_other0','value'=>'0','required'=>'true','checked'=>'true','extra'=>'onchange="billing_check();"'))?>&nbsp;&nbsp;&nbsp;
						
						<?=form_checkbox(array('type'=>'radio','title'=>'Bill Third Party (Details Below)','name'=>'bill_other','id'=>'bill_other1','value'=>'1','checked'=>'false','extra'=>'onchange="billing_check();"'))?>
						
						<div id="other_bill" style="display:none;">
						<div class="colHeader">Third Party Billing Information</div>
						
						<?=form_text(array('caption'=>'Name','name'=>'bill_name','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'Address','name'=>'bill_address1','size'=>'30','maxlength'=>'200'))?>
						<div class="FormField"><?=form_text(array('caption'=>'Address','nocaption'=>'true','name'=>'bill_address2','size'=>'30','maxlength'=>'200'))?></div>
						<div class="FormField"><?=form_text(array('caption'=>'Address','nocaption'=>'true','name'=>'bill_address3','size'=>'30','maxlength'=>'200'))?></div>
						
						<?=form_text(array('caption'=>'City / Local','name'=>'bill_local','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'State','name'=>'bill_state','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'Postal','name'=>'bill_postal','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'Country','name'=>'bill_country','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'Phone','name'=>'bill_phone','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'Fax','name'=>'bill_fax','size'=>'30','maxlength'=>'200'))?>
						<?=form_text(array('caption'=>'E-mail Address','name'=>'bill_email','size'=>'30','maxlength'=>'200'))?>
						</div>
<?				
				}
?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
<?					
						$temprooms = db_query("SELECT rooms.id, room_name AS name, buildings.building_name AS optGroup FROM rooms JOIN buildings ON rooms.building_id=buildings.id WHERE !rooms.deleted ORDER BY optGroup, name","Getting Rooms");
						$rooms = array();
						while($row = mysqli_fetch_assoc($temprooms)) {
							$rooms[] = array('value'=>$row['id'],'display'=>$row['name'],'optGroup'=>$row['optGroup']);
						}
?>

						<div class="colHeader" style="width:100%;">Dates/Times</div>
						
						<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
							<thead>
								<tr>
									<th>Description</th>
									<th>Date</th>
									<th>Prep Start</th>
									<th>Start Time</th>
									<th>End Time</th>
									<th>Strike By</th>
									<th>Room</th>
								</tr>
							</thead>
							<tbody>
<?
						$i = 0;
						while($i++ < $time_slots) {		
?>							
								<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
									<td><?=form_text(array('caption'=>'Room','nocaption'=>'true','name'=>'description_'.$i,'size'=>'30','maxlength'=>'200'))?></td>
									<td><?=form_text(array('caption'=>'Date','nocaption'=>'true','name'=>'date_slot_'.$i,'size'=>'10','maxlength'=>'20'))?></td>
									<td><?=form_text(array('caption'=>'Prep Start','nocaption'=>'true','name'=>'prep_time_slot_'.$i,'size'=>'7','maxlength'=>'20'))?></td>
									<td><?=form_text(array('caption'=>'Start Time','nocaption'=>'true','name'=>'start_time_slot_'.$i,'size'=>'7','maxlength'=>'20'))?></td>
									<td><?=form_text(array('caption'=>'End Time','nocaption'=>'true','name'=>'end_time_slot_'.$i,'size'=>'7','maxlength'=>'20'))?></td>
									<td><?=form_text(array('caption'=>'Stike By','nocaption'=>'true','name'=>'strike_time_slot_'.$i,'size'=>'7','maxlength'=>'20'))?></td>
									<td><?=form_select($rooms,array('type'=>'grouparray','nocaption'=>'true','caption'=>'-Select Room-','required'=>'true','name'=>'room_id'.$i))?></td>
								</tr>
<?
						}
?>			

							</tbody>
						</table>
					
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