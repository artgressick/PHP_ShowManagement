<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Session_Requests_".$now).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Session Name</td>
			<td style="font-weight:bold;">Start Date</td>
			<td style="font-weight:bold;">Start Time</td>
			<td style="font-weight:bold;">End Time</td>
			<td style="font-weight:bold;">Session Type</td>
			<td style="font-weight:bold;">Job-code</td>
			<td style="font-weight:bold;">Order Status</td>
			<td style="font-weight:bold;">Speaker</td>
			<td style="font-weight:bold;">Bill To</td>
			<td style="font-weight:bold;">Room Number</td>
			<td style="font-weight:bold;">Building Name</td>
<?
		mysqli_data_seek($pt,0);
		while($pt_row = mysqli_fetch_assoc($pt)) {
?>
			<td style="font-weight:bold;"><?=$pt_row['product_type']?> Products</td>
			<td style="font-weight:bold;"><?=$pt_row['product_type']?> Custom Requests</td>
			<td style="font-weight:bold;"><?=$pt_row['product_type']?> Questions</td>
<?
		}
?>
			<td style="font-weight:bold;">Presenter Bringing</td>
			<td style="font-weight:bold;">Order Notes</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['class_name'])?></td>
			<td><?=pretty_date($row['start_date'])?></td>
			<td><?=pretty_time($row['start_time'])?></td>
			<td><?=pretty_time($row['end_time'])?></td>
			<td><?=decode($row['sessiontype_name'])?></td>
			<td><?=decode($row['jobcode'])?></td>
			<td><?=decode($row['order_status'])?></td>
			<td><?=decode($row['speaker'])?></td>
			<td><?=($row['bill_other']?'Third Party':'Main Budget')?></td>
			<td><?=decode($row['room_number'])?></td>
			<td><?=decode($row['building_name'])?></td>
<?
		mysqli_data_seek($pt,0);
		while($pt_row = mysqli_fetch_assoc($pt)) {
?>
			<td><?=decode($row['products_'.$pt_row['id']])?></td>
			<td><?=decode($row['custom_'.$pt_row['id']])?></td>
			<td><?=decode($row['questions_'.$pt_row['id']])?></td>
<?
		}
?>
			<td><?=decode($row['presenter_bringing'])?></td>
			<td><?=decode($row['Order_Notes'])?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>