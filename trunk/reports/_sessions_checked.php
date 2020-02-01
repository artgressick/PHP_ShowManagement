<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Sessions_Checked_".$_REQUEST['date']).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Session Name</td>
			<td style="font-weight:bold;">Session Type</td>
			<td style="font-weight:bold;">Room Name</td>
			<td style="font-weight:bold;">Building Name</td>
			<td style="font-weight:bold;">Prep Time</td>
			<td style="font-weight:bold;">Start Time</td>
			<td style="font-weight:bold;">Checked In At</td>
			<td style="font-weight:bold;">Checked By</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['class_name'])?></td>
			<td><?=decode($row['sessiontype_name'])?></td>
			<td><?=decode($row['room_name'])?></td>
			<td><?=decode($row['building_name'])?></td>
			<td><?=pretty_time($row['prep_time'])?></td>
			<td><?=pretty_time($row['start_time'])?></td>
			<td><?=($row['checked_datetime'] != "" ? decode(pretty_datetime($row['checked_datetime'])) : "")?></td>
			<td><?=decode($row['full_name'])?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>