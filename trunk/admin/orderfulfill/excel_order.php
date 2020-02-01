<?php
	$NON_HTML_PAGE = true;
	include('_controller.php');

	$now = date('m-d-Y');
	ob_clean(); 	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".str_replace(" ","_",$orderinfo['quote_name'].'-'.$orderinfo['revision']).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");

?>
	<table>
		<tr>
			<td style="height:15px; width:10px;">&nbsp;</td>
			<td style="height:15px; width:355px;">&nbsp;</td>
			<td style="height:15px; width:50px;">&nbsp;</td>
			<td style="height:15px; width:50px;">&nbsp;</td>
			<td style="height:15px; width:50px;">&nbsp;</td>
			<td style="height:15px; width:50px;">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border:1px solid black; border-bottom: none; font-size:16px; font-weight:bold; height:40px; vertical-align:middle; width:400px;">NECC 2009 - Session Quote</td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-left:1px solid black; border-right:1px solid black;">Job Code: <?=$orderinfo['quote_name'].'-'.$orderinfo['revision']?></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-left:1px solid black; border-right:1px solid black;">Show: <?=decode($_SESSION['show_name'])?></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-left:1px solid black; border-right:1px solid black;">Session: <?=decode($info['class_name'])?></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-left:1px solid black; border-right:1px solid black;">Client/Company: <?=decode($info['bill_name']).' ('.decode($info['bill_email']).')'?></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-left:1px solid black; border-right:1px solid black;">Phone #: <?=$info['bill_phone']?></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" style="border-left:1px solid black; border-right:1px solid black; border-bottom: 1px solid black;">Quote Version: <?=$orderinfo['revision'].'/'.date('Y-m-d',strtotime($orderinfo['updated_at'])).'/SM'?></td>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6" style="height:15px;">&nbsp;</td>
		</tr>
<?
	//Now for the Query
	$q = "SELECT TS.id, TS.start_date, TS.start_time, TS.end_time, TS.description AS tsdescription, R.room_number, R.description, B.building_name, PT.id AS pt_id, PT.product_type, P.product_name, P.common_name, SOI.quantity, SOI.price, SOI.setup
	
			FROM product_types AS PT
			JOIN products AS P ON P.producttype_id=PT.id
			JOIN session_order_items AS SOI ON SOI.product_id=P.id AND SOI.session_id='".$info['id']."'
			JOIN time_slots AS TS ON SOI.timeslot_id=TS.id
			JOIN rooms as R ON TS.room_id=R.id
			JOIN buildings AS B ON R.building_id=B.id
			
			WHERE !R.deleted AND !PT.deleted AND PT.enabled AND !P.deleted AND P.enabled AND !SOI.deleted AND SOI.approved
			
			ORDER BY TS.start_date, TS.start_time, TS.end_time, PT.product_type, P.product_name, P.common_name
		";
	
	$products = db_query($q,"Get Products");
	
	//set values to blank
	$total = array('product'=>0,'setup'=>0);
	$tsid = '';
	while($row = mysqli_fetch_assoc($products)) {
		if($tsid != $row['id']) {
?>
			<tr>
				<td>&nbsp;</td>
				<td colspan="5" style="border:1px solid black; border-bottom: none; background: #AAAAAA; font-size:14px; font-weight:bold; height:25px; vertical-align:middle;"><?=date('l, F j Y',strtotime($row['start_date'])).' from '.pretty_time($row['start_time']).' to '.pretty_time($row['end_time']).(strlen($row['tsdescription']) > 2 ?' ('.decode($row['tsdescription']).')':'')?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="5" style="border:1px solid black; border-bottom: none; background: #AAAAAA; font-size:14px; font-weight:bold; height:25px; vertical-align:middle;">Room: <?=decode($row['room_number']).' ('.decode($row['description']).') - Building: '.decode($row['building_name'])?></td>
			</tr>
<?
			$tsid = $row['id'];
			$ptid = '';
		}
		if($ptid != $row['pt_id']) {
?>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td style="height:20px; border:1px solid black; border-right:none; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; width:355px;"><?=decode($row['product_type'])?> Products</td>
				<td style="height:20px; border:1px solid black; border-right:none; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:center; width:50px;">Qty.</td>
				<td style="height:20px; border:1px solid black; border-right:none; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:center; width:50px;">Price</td>
				<td style="height:20px; border:1px solid black; border-right:none; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:center; width:50px;">Setup</td>
				<td style="height:20px; border:1px solid black; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:center; width:50px;">Total</td>
			</tr>
<?
			$ptid = $row['pt_id'];
		}
?>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td style="height:20px; border-left:1px solid black; border-bottom:1px solid black; vertical-align:middle; width:355px;"><?=decode($row['product_name']).' ('.decode($row['common_name']).')'?></td>
				<td style="height:20px; border-left:1px solid black; border-bottom:1px solid black; vertical-align:middle; text-align:center; width:50px;"><?=$row['quantity']?></td>
				<td style="height:20px; border-left:1px solid black; border-bottom:1px solid black; vertical-align:middle; text-align:center; width:50px;"><?='$'.number_format($row['price'],2,'.',',')?></td>
				<td style="height:20px; border-left:1px solid black; border-bottom:1px solid black; vertical-align:middle; text-align:center; width:50px;"><?='$'.number_format($row['setup'],2,'.',',')?></td>
				<td style="height:20px; border:1px solid black; border-top:none; vertical-align:middle; text-align:center; width:50px; background:#DDDDDD;"><?='$'.number_format(($row['quantity'] * ($row['price'] + $row['setup'])),2,'.',',')?></td>
<?
		$total['product'] += ($row['quantity'] * $row['price']);
		$total['setup'] += ($row['quantity'] * $row['setup']);
	} 
?>
		<tr>
			<td colspan="6" style="height:15px;">&nbsp;</td>
		</tr>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td colspan="3" style="height:20px; border:1px solid black; border-right:none; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; width:355px;">Summary</td>
				<td colspan="2" style="height:20px; border:1px solid black; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:center; width:50px;">Total</td>
			</tr>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td colspan="3" style="height:20px; border:1px solid black; border-right:none; font-size:14px; font-weight:bold; vertical-align:middle; width:355px;">Products</td>
				<td colspan="2" style="height:20px; border:1px solid black; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:right; width:50px;"><?='$'.number_format($total['product'],2,'.',',')?></td>
			</tr>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td colspan="3" style="height:20px; border:1px solid black; border-right:none; font-size:14px; font-weight:bold; vertical-align:middle; width:355px;">Setup</td>
				<td colspan="2" style="height:20px; border:1px solid black; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:right; width:50px;"><?='$'.number_format($total['setup'],2,'.',',')?></td>
			</tr>
<?
	$cont = (($orderinfo['contingency'] / 100) * ($total['product'] + $total['setup']));
?>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td colspan="3" style="height:20px; border:1px solid black; border-right:none; font-size:14px; font-weight:bold; vertical-align:middle; width:355px;">Contingency (<?=$orderinfo['contingency']?>%)</td>
				<td colspan="2" style="height:20px; border:1px solid black; background: #DDDDDD; font-size:14px; font-weight:bold; vertical-align:middle; text-align:right; width:50px;"><?='$'.number_format($cont,2,'.',',')?></td>
			</tr>
			<tr>
				<td style="height:20px;">&nbsp;</td>
				<td colspan="3" style="height:20px; border:1px solid black; border-right:none; font-size:16px; font-weight:bold; vertical-align:middle; width:355px; text-align:right;">Order Total</td>
				<td colspan="2" style="height:20px; border:1px solid black; background: #DDDDDD; font-size:16px; font-weight:bold; vertical-align:middle; text-align:right; width:50px;"><?='$'.number_format(($total['product'] + $total['setup'] + $cont),2,'.',',')?></td>
			</tr>
		</table>
<?
	exit;
	die();
?>