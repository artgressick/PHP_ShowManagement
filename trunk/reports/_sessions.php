<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Sessions_".$_REQUEST['date']).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Session Name</td>
			<td style="font-weight:bold;">Session Type</td>
			<td style="font-weight:bold;">Room Name</td>
			<td style="font-weight:bold;">Room Description</td>
			<td style="font-weight:bold;">Building Name</td>
			<td style="font-weight:bold;">Prep Time</td>
			<td style="font-weight:bold;">Start Time</td>
			<td style="font-weight:bold;">End Time</td>
			<td style="font-weight:bold;">Strike Time</td>
			<td style="font-weight:bold;">Time Description</td>
			<td style="font-weight:bold;">Speaker</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['class_name'])?></td>
			<td><?=decode($row['sessiontype_name'])?></td>
			<td><?=decode($row['room_name'])?></td>
			<td><?=decode($row['room_description'])?></td>
			<td><?=decode($row['building_name'])?></td>
			<td><?=pretty_time($row['prep_time'])?></td>
			<td><?=pretty_time($row['start_time'])?></td>
			<td><?=pretty_time($row['end_time'])?></td>
			<td><?=pretty_time($row['strike_time'])?></td>
			<td><?=pretty_time($row['description'])?></td>
			<td><?=pretty_time($row['speaker'])?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>