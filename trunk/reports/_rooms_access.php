<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Room_Access_".$now).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Room Name</td>
			<td style="font-weight:bold;">Room Number</td>	
			<td style="font-weight:bold;">Room Description</td>
			<td style="font-weight:bold;">Building Name</td>
			<td style="font-weight:bold;">Move In</td>
			<td style="font-weight:bold;">Move Out</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['room_name'])?></td>
			<td><?=decode($row['room_number'])?></td>
			<td><?=decode($row['description'])?></td>
			<td><?=decode($row['building_name'])?></td>
			<td><?=decode(pretty_datetime($row['move_in']))?></td>
			<td><?=decode(pretty_datetime($row['move_out']))?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>