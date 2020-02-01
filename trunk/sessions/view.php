<?
	# 12/21/2009 - Wesley Grimes - Added Financial Responsibility Information - Line 96-122
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info,$time_slots,$show_data;
	
?>
	<table cellpadding="0" cellspacing="0" class="tabs">
		<tr>
			<th class="current" onclick="location.href='view.php?key=<?=$info['lkey']?>';">Information</th><th class="space"><!-- BLANK --></th>
			<th class="tab" onclick="location.href='order.php?key=<?=$info['lkey']?>';">Session Products</th><th class="space"><!-- BLANK --></th>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" class="page_info">
		<tr>
			<td class="headleft"><!-- BLANK --></td>
			<td class="headmiddle" style="padding-left:5px;">Session Information<?=($_SESSION['admin_access'] == 1?" (".linkto(array("display"=>"Edit","address"=>"admin/sessions/edit.php?key=".$info['lkey'])).")":"")?></td>
			<td class="headright"><!-- BLANK --></td>
		</tr>
		<tr>
			<td class="body" colspan="3">
				<table cellpadding="0" cellspacing="0" style="width:100%;">
					<tr>
						<td style="width:50%; vertical-align:top;">
			<?=form_text(array('caption'=>'Session Name','display'=>'true','name'=>'class_name','value'=>$info['class_name']))?>
			<?=form_text(array('caption'=>'Session Number','display'=>'true','name'=>'class_number','value'=>$info['class_number']))?>

<?					
			$types = db_query("SELECT id, sessiontype_name AS name FROM sessiontypes WHERE !deleted ORDER BY name","Getting Types");
?>
			<?=form_select($types,array('caption'=>'Session Type','display'=>'true','name'=>'sessiontype_id','value'=>$info['sessiontype_id']))?>

			<?=form_text(array('caption'=>'Speaker','display'=>'true','name'=>'speaker','value'=>$info['speaker']))?>
			
			<?=form_text(array('caption'=>'Speaker Email','display'=>'true','name'=>'speaker_email','value'=>$info['speaker_email']))?>

			<?=form_text(array('caption'=>'Audience Size','display'=>'true','name'=>'audience_size','value'=>$info['audience_size']))?>
			
			<?=form_text(array('caption'=>'Room Area','display'=>'true','name'=>'room_area','value'=>$info['room_area']))?>

			<?=form_text(array('caption'=>'Session Description','display'=>'true','name'=>'description','value'=>$info['description']))?>
										</td>
										<td style="width:50%; vertical-align:top;">

			<div class="colHeader" style="width:100%;">Notes</div>
			
			<?=form_text(array('caption'=>'Notes','display'=>'true','name'=>'notes','value'=>$info['notes']))?>

<?
		$files = db_query("SELECT * FROM files WHERE class_id=".$info['id'],"Getting Files");
		if(mysqli_num_rows($files) > 0) {
?>
				<div class="colHeader" style="width:100%;">Current Diagram(s)</div>
<?	
			while($row = mysqli_fetch_assoc($files)) {
?>			
				<div><a href='<?=$BF?>files/sessions/<?=$row['file_name']?>'><img src='<?=$BF?>images/pdficon.jpg' alt='<?=$row['file_name']?>' target="_blank" /> <?=$row['file_name']?></a></div>

<?
			}
?>
<?	
		}
?>

						<div class="colHeader" style="width:100%;">Billing Information</div>
						<?=form_text(array('caption'=>'Bill To','display'=>'true','value'=>($info['bill_other']?'Third Party':'Main Budget')))?>
<?
					if($info['bill_other']) {
?>						
						<div class="colHeader" style="width:100%;">Third Party Billing Information</div>
						<table cellpadding="0" cellspacing="0" width="100%;">
							<tr>
								<td style="vertical-align:top;">
						<?=form_text(array('caption'=>'Name','display'=>'true','value'=>$info['bill_name']))?>
						<?=form_text(array('caption'=>'Address','display'=>'true','value'=>$info['bill_address1'].' '.$info['bill_address2'].' '.$info['bill_address3'] ))?>
						
						<?=form_text(array('caption'=>'City / Local','display'=>'true','value'=>$info['bill_local']))?>
						<?=form_text(array('caption'=>'State','display'=>'true','value'=>$info['bill_state']))?>
						<?=form_text(array('caption'=>'Postal','display'=>'true','value'=>$info['bill_postal']))?>
								</td>
								<td style="vertical-align:top;">
						<?=form_text(array('caption'=>'Country','display'=>'true','value'=>$info['bill_country']))?>
						<?=form_text(array('caption'=>'Phone','display'=>'true','value'=>$info['bill_phone']))?>
						<?=form_text(array('caption'=>'Fax','display'=>'true','value'=>$info['bill_fax']))?>
						<?=form_text(array('caption'=>'E-mail Address','display'=>'true','value'=>$info['bill_email']))?>
								</td>
							</tr>
						</table>
<?
					}
?>

<?
					if($info['financial_responsibility'] != -1000) {
?>						
						<div class="colHeader" style="width:100%;">Financial Responsibility Information</div>
						<table cellpadding="0" cellspacing="0" width="100%;">
							<tr>
								<td style="vertical-align:top;">
						<?=form_text(array('caption'=>'Name','display'=>'true','value'=>$info['fin_name']))?>
						<?=form_text(array('caption'=>'Email','display'=>'true','value'=>$info['fin_email'] ))?>
								</td>
							</tr>
						</table>
<?
					} else {
?>

						<div class="colHeader" style="width:100%;">Financial Responsibility Information</div>
						<table cellpadding="0" cellspacing="0" width="100%;">
							<tr>
								<td style="vertical-align:top;">
								Not Available
								</td>
							</tr>
						</table>

<?
					}
?>

						</td>
					</tr>
		<tr>
			<td colspan="3">
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
				$datestimes = db_query('SELECT time_slots.*, rooms.room_name FROM time_slots JOIN rooms ON time_slots.room_id=rooms.id WHERE !time_slots.deleted AND time_slots.class_id='.$info['id'].' ORDER BY time_slots.start_date, time_slots.prep_time, time_slots.start_time, time_slots.end_time, time_slots.strike_time,description','Getting Dates and Times');
				$i = 1;
				while($row = mysqli_fetch_assoc($datestimes)) {		
?>							
					<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
						<td class="nocursor"><?=$row['description']?></td>
						<td class="nocursor"><?=pretty_date($row['start_date'])?></td>
						<td class="nocursor"><?=pretty_time($row['prep_time'])?></td>
						<td class="nocursor"><?=pretty_time($row['start_time'])?></td>
						<td class="nocursor"><?=pretty_time($row['end_time'])?></td>
						<td class="nocursor"><?=pretty_time($row['strike_time'])?></td>
						<td class="nocursor"><?=$row['room_name']?></td>
					</tr>
<?
					$i++;
				}
?>			
				</tbody>
			</table>
			
			</td>
		</tr>
				</table>
			</td>
		</tr>
	</table>
					
<?
	}
?>