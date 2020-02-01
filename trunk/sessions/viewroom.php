<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
			<div class="colHeader2">Room Information</div>

			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
				
						<?=form_text(array('caption'=>'Room Name','value'=>$info['room_name'],'display'=>'true'))?>

						<?=form_text(array('caption'=>'Room Number','value'=>$info['room_number'],'display'=>'true'))?>

<?					
						$building = db_query("SELECT id, building_name as name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Getting Buildings");
?>
						<?=form_select($building,array('caption'=>'Building','value'=>$info['building_id'],'display'=>'true'))?>

<?					
						$platforms = db_query("SELECT id, platform_name as name FROM platforms ORDER BY id","Getting platforms");
?>
						<?=form_select($platforms,array('caption'=>'Platform','required'=>'Indicates what types of computers are in this room','value'=>$info['platform_id'],'display'=>'true'))?>

						<?=form_text(array('caption'=>'Has Internet Access','value'=>(!$info['internet_access']?'NO':'Yes'),'display'=>'true'))?>

						<?=form_text(array('caption'=>'Capacity','value'=>$info['capacity'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Square Footage','value'=>$info['square_feet'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Dimensions','value'=>$info['dimensions'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Public Location','value'=>$info['public_location'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Default Setup','value'=>$info['default_setup'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Category','value'=>$info['category'],'display'=>'true'))?>
						
					</td>
					<td class="tcgutter"></td>
					<td class="tcright">

						<table cellpadding="0" cellspacing="0">
							<tr>
								<td><?=form_text(array('caption'=>'Move-in Date','value'=>date('Y-m-d',strtotime($info['move_in'])),'display'=>'true'))?></td>
								<td width="5">&nbsp;</td>
								<td><?=form_text(array('caption'=>'Move-in Time','value'=>date('g:i a',strtotime($info['move_in'])),'display'=>'true'))?></td>
							</tr>
							<tr>
								<td><?=form_text(array('caption'=>'Move-out Date','value'=>date('Y-m-d',strtotime($info['move_out'])),'display'=>'true'))?></td>
								<td width="5">&nbsp;</td>
								<td><?=form_text(array('caption'=>'Move-out Time','value'=>date('g:i a',strtotime($info['move_out'])),'display'=>'true'))?></td>
							</tr>
						</table>

						<?=form_text(array('caption'=>'Room Description','value'=>$info['description'],'display'=>'true'))?>

					</td>
				</tr>
			</table>
			</div>
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