<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Product_Tracking_".$now).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Tracking Number</td>
			<td style="font-weight:bold;">Product</td>	
			<td style="font-weight:bold;">Type</td>
			<td style="font-weight:bold;">Checked Out On</td>
			<td style="font-weight:bold;">Checked Out By</td>
			<td style="font-weight:bold;">Room</td>
			<td style="font-weight:bold;">Building</td>
			<td style="font-weight:bold;">Checked In On</td>
			<td style="font-weight:bold;">Checked In By</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['tracking_number'])?></td>
			<td><?=decode($row['product_name'])?></td>
			<td><?=decode($row['product_type'])?></td>
			<td><?=decode($row['check_out'])?></td>
			<td><?=decode($row['user_name'])?></td>
			<td><?=decode($row['room_name'])?></td>
			<td><?=decode($row['building_name'])?></td>
			<td><?=decode($row['check_in'])?></td>
			<td><?=decode($row['user_name2'])?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>