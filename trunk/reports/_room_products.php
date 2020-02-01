<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Room_Products_".$now).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Room Name</td>
			<td style="font-weight:bold;">Description</td>	
			<td style="font-weight:bold;">Building Name</td>
			<td style="font-weight:bold;">Product Name</td>
			<td style="font-weight:bold;">Common Name</td>
			<td style="font-weight:bold;">Product Type</td>
			<td style="font-weight:bold;">Quantity</td>
<?
		if($_SESSION['group_id'] == 1 && $_REQUEST['show_vendor'] == 1) {
?>
			<td style="font-weight:bold;">Vendor</td>
<?			
		}
?>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['room_name'])?></td>
			<td><?=decode($row['description'])?></td>
			<td><?=decode($row['building_name'])?></td>
			<td><?=decode($row['product_name'])?></td>
			<td><?=decode($row['common_name'])?></td>
			<td><?=decode($row['product_type'])?></td>
			<td><?=decode($row['quantity'])?></td>
<?
		if($_SESSION['group_id'] == 1 && $_REQUEST['show_vendor'] == 1) {
?>
			<td><?=decode($row['vendor_name'])?></td>
<?			
		}
?>

		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>