<?php
	$BF = '../';
	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$_SESSION['show_name']."_Product_List_".$now).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<table border="1">
		<tr>
			<td style="font-weight:bold;">Product Name</td>
			<td style="font-weight:bold;">Common Name</td>	
			<td style="font-weight:bold;">Product Type</td>
		</tr>
<?
	$count=0;
	while($row = mysqli_fetch_assoc($results)) {
?>
		<tr>
			<td><?=decode($row['product_name'])?></td>
			<td><?=decode($row['common_name'])?></td>
			<td><?=decode($row['product_type'])?></td>
		</tr>
<?
	}
?>
	</table>
<?
	exit;
	die();
?>