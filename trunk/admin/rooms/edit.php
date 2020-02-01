<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<table cellpadding="0" cellspacing="0" class="tabs">
		<tr>
			<th class="current" onclick="location.href='edit.php?key=<?=$info['lkey']?>';">Information</th><th class="space"><!-- BLANK --></th>
			<th class="tab" onclick="location.href='products.php?key=<?=$info['lkey']?>';">Room Products</th><th class="space"><!-- BLANK --></th>
		</tr>
	</table>
	<div class='innerbody'>
		<form enctype="multipart/form-data" action="" method="post" id="idForm" onsubmit="return error_check()">

			<div class="colHeader2">Room Information</div>

			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
				
						<?=form_text(array('caption'=>'Room Name','required'=>'true','name'=>'room_name','size'=>'30','maxlength'=>'200','value'=>$info['room_name']))?>

						<?=form_text(array('caption'=>'Room Number','required'=>'true','name'=>'room_number','size'=>'30','maxlength'=>'200','value'=>$info['room_number']))?>

<?					
						$building = db_query("SELECT id, building_name as name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Getting Buildings");
?>
						<?=form_select($building,array('caption'=>'Building','required'=>'true','name'=>'building_id','value'=>$info['building_id']))?>

<?					
						$platforms = db_query("SELECT id, platform_name as name FROM platforms ORDER BY id","Getting platforms");
?>
						<?=form_select($platforms,array('caption'=>'Platform','required'=>'Required) (Indicates what types of computers are in this room','name'=>'platform_id','value'=>$info['platform_id']))?>

						<?=form_checkbox(array('type'=>'radio','caption'=>'Has Internet Access','title'=>'No','name'=>'internet_access','id'=>'internet_access0','value'=>'0','checked'=>(!$info['internet_access']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'internet_access','id'=>'internet_access1','value'=>'1','checked'=>($info['internet_access']?'true':'false')))?>

						
						<?=form_text(array('caption'=>'Capacity','name'=>'capacity','size'=>'10','maxlength'=>'10','value'=>$info['capacity']))?>
						
						<?=form_text(array('caption'=>'Square Footage','name'=>'square_feet','size'=>'10','maxlength'=>'10','value'=>$info['square_feet']))?>
						
						<?=form_text(array('caption'=>'Dimensions','name'=>'dimensions','size'=>'15','maxlength'=>'20','value'=>$info['dimensions']))?>
						
						<?=form_text(array('caption'=>'Public Location','name'=>'public_location','size'=>'30','maxlength'=>'200','value'=>$info['public_location']))?>
						
						<?=form_text(array('caption'=>'Default Setup','name'=>'default_setup','size'=>'30','maxlength'=>'255','value'=>$info['default_setup']))?>
						
						<?=form_text(array('caption'=>'Category','name'=>'category','size'=>'30','maxlength'=>'255','value'=>$info['category']))?>
						
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
								<td><?=form_text(array('caption'=>'Move-in Date','name'=>'move_in-date','size'=>'10','maxlength'=>'100','value'=>date('Y-m-d',strtotime($info['move_in']))))?></td>
								<td width="5">&nbsp;</td>
								<td><?=form_text(array('caption'=>'Move-in Time','name'=>'move_in-time','size'=>'10','maxlength'=>'100','value'=>date('g:i a',strtotime($info['move_in']))))?></td>
							</tr>
							<tr>
								<td><?=form_text(array('caption'=>'Move-out Date','name'=>'move_out-date','size'=>'10','maxlength'=>'100','value'=>date('Y-m-d',strtotime($info['move_out']))))?></td>
								<td width="5">&nbsp;</td>
								<td><?=form_text(array('caption'=>'Move-out Time','name'=>'move_out-time','size'=>'10','maxlength'=>'100','value'=>date('g:i a',strtotime($info['move_out']))))?></td>
							</tr>
						</table>

						<?=form_textarea(array('caption'=>'Room Description','name'=>'description','cols'=>'60','rows'=>'10','value'=>$info['description']))?>

						<div class="colHeader">Notes</div>

						<?=form_textarea(array('caption'=>'Standard Room Set Notes (displays on order page under room set)','name'=>'notes','cols'=>'60','rows'=>'10','value'=>$info['notes']))?>

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
		$files = db_query("SELECT * FROM room_files WHERE room_id=".$info['id'],"Getting Files");
		if(mysqli_num_rows($files) > 0) {
?>
			<div style="margin-top:20px;" class="innerbody">
				<div class="colHeader2">Current Diagram(s)</div>
<?	
			while($row = mysqli_fetch_assoc($files)) {
?>			
				<div><a href='<?=$BF?>files/rooms/<?=$row['file_name']?>'><img src='<?=$BF?>images/pdficon.jpg' alt='<?=$row['file_name']?>' target="_blank" /> <?=$row['file_name']?></a></div>

<?
			}
?>
			</div>
<?	
		}
	}
?>