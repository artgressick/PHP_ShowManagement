<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_No_Orders_".$now).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Session Name</td>
			<td style="font-weight:bold;">Session Type</td>	
			<td style="font-weight:bold;">Speaker</td>
			<td style="font-weight:bold;">Bill To</td>
			<td style="font-weight:bold;">Starting Date/Time</td>
			<td style="font-weight:bold;"># Sessions</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['class_name'])?></td>
			<td><?=decode($row['sessiontype_name'])?></td>
			<td><?=decode($row['speaker'])?></td>
			<td><?=decode($row['billto'])?></td>
			<td><?=decode(pretty_datetime($row['start_dt']))?></td>
			<td><?=decode($row['time_count'])?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>