<?php
	include('_controller.php');

	function sitm() {
		global $BF,$info;

?>
		<div class='innerbody'>
			<div class="colHeader">Session Information</div>
		
			<table cellpadding="0" cellspacing="0" style="width:100%;">
				<tr>
					<td style="padding:3px; width:50%; vertical-align:top;">
						<?=form_text(array('caption'=>'Session Name','value'=>$info['class_name'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Session Description','value'=>$info['cdescription'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Room (Building)','value'=>$info['room_name'].' ('.$info['building_name'].')','display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Speaker','value'=>$info['speaker'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Audience Size','value'=>$info['audience_size'],'display'=>'true'))?>

						<?=form_text(array('caption'=>'Session Date','value'=>pretty_date($info['start_date']),'display'=>'true'))?>
						
					</td>
					<td style="padding:3px; width:50%; vertical-align:top;">
	
						<?=form_text(array('caption'=>'Prep Time','value'=>pretty_time($info['prep_time']),'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Start Time','value'=>pretty_time($info['start_time']),'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'End Time','value'=>pretty_time($info['end_time']),'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Strike Time','value'=>pretty_time($info['strike_time']),'display'=>'true'))?>

						<div style="padding-top:20px; text-align:right;"><input type="button" id="show_btn" value="Show More Details" onclick="show_more();" /></div>
					</td>
				</tr>
				<tr id="more_data" style="display:none;">
					<td style="padding:3px; width:50%; vertical-align:top;">

						<?=form_text(array('caption'=>'Session Notes','value'=>$info['cnotes'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Billing Party','value'=>($info['bill_other']?$info['bill_name']:'Main Budget'),'display'=>'true'))?>

					</td>
					<td style="padding:3px; width:50%; vertical-align:top;">

						<?=form_text(array('caption'=>'Room Description','value'=>$info['rdescription'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Room Has Internet Access','value'=>(!$info['internet_access']?'NO':'Yes'),'display'=>'true'))?>

						<?=form_text(array('caption'=>'Room Capacity','value'=>$info['capacity'],'display'=>'true'))?>

						<?=form_text(array('caption'=>'Room Sq. Feet','value'=>$info['square_feet'],'display'=>'true'))?>

<?
	$room_checked = "N/A";
	if($info['rchecked_datetime'] != "") {
		$room_checked = $info['rfirst_name'].' '.$info['rlast_name'];
		$phone = preg_replace('/(\||\'|\"|\.|\@\,| |\-|\(|\)|\+|\-|[a-zA-Z])+/','',$info['rcellnumber']);
		if(strlen($phone) == 11 && $phone[0] == "1") { $phone = substr($phone, 1); }
		if(strlen($phone) == 10 && $info['rtext_method'] != '') {
		$msg = 'To: '.$info['rfirst_name'].' '.$info['rlast_name'].' -- From: '.$_SESSION['first_name'].' '.$_SESSION['last_name'].' -- Room: '.$info['room_name'].' -- Building: '.$info['building_name'].' -- Call Back: '.$_SESSION['mobile'];
				
		$room_checked .= "&nbsp;&nbsp;&nbsp;<input type='button' id='text_tech1' value='Request Assistance' onclick=\"text_tech('".$BF."','".$_SESSION['user_email']."','".$phone.$info['rtext_method']."','".$msg."','text_tech1')\" />";
		}
	}
?>

						<?=form_text(array('caption'=>'Room Checked In By:','value'=>$room_checked,'display'=>'true'))?>

					</td>
				</tr>
			</table>
		</div>
<?
		$files = db_query("SELECT * FROM files WHERE class_id=".$info['class_id'],"Getting Files");
		if(mysqli_num_rows($files) > 0) {
?>
		<div style="margin-top:10px;" class="innerbody">
			<div class="colHeader">Diagram(s)</div>
<?	
			while($row = mysqli_fetch_assoc($files)) {
?>			
				<div style='padding:5px 5px;'><a href='<?=$BF?>files/sessions/<?=$row['file_name']?>'><?=$row['file_name']?></a></div>

<?
			}
?>
			</div>
<?	
		}


	$products = db_query("SELECT soi.*, CONCAT('<strong>',products.product_name,'<strong> <span style=\"font-size:10px;\">(',products.common_name,')</span>') AS product_name, product_types.product_type, soi.quantity 
					FROM session_order_items AS soi
					JOIN products ON soi.product_id=products.id
					JOIN product_types ON products.producttype_id = product_types.id
					WHERE !soi.deleted AND products.enabled AND product_types.enabled AND soi.timeslot_id=".$info['id']."
					ORDER BY product_type, products.product_name","Getting Products for Session;")
?>
		<div class='innerbody' style="margin-top: 10px;">
			<div class="colHeader">Session Products</div>
			<table cellpadding="3" cellspacing="0" style="width:100%;" class="List">
				<tr>
					<th>Product</th>
					<th style="width:10px; white-space:nowrap;">Qty</th>
				</tr>
<?
		$product_type = '';
		$cnt = 0;
		while($row = mysqli_fetch_assoc($products)) {
			if($product_type != $row['product_type']) {
?>
				<tr class="ListHeader">
					<td colspan="5" style="font-weight:bold;">
						<?=$row['product_type']?>
					</td>
				</tr>
<?
				$product_type = $row['product_type'];
			}
?>
				<tr class='<?=($cnt++%2 ? 'ListOdd' : 'ListEven')?>'>
					<td><?=decode($row['product_name'])?></td>
					<td style="text-align:center; vertical-align:middle;"><?=decode($row['quantity'])?></td>
				</tr>
<?		
		}
		if($cnt == 0) {
?>
				<tr class='<?=($cnt++%2 ? 'ListOdd' : 'ListEven')?>'>
					<td colspan="5" style="text-align:center;">
						<em>No products assigned to this Session!</em>
					</td>
				</tr>
<?		
		}
?>

			</table>
		</div>
		<div style="margin-top:10px;"><input type="button" id='btn_room_products' value="Show Room Products" onclick="show_rproducts();" /></div>
<?
	$products = db_query("SELECT room_products.*, CONCAT('<strong>',products.product_name,'<strong> <span style=\"font-size:10px;\">(',products.common_name,')</span>') AS product_name, product_types.product_type, room_products.quantity 
					FROM room_products
					JOIN products ON room_products.product_id=products.id
					JOIN product_types ON products.producttype_id = product_types.id
					WHERE !room_products.deleted AND products.enabled AND product_types.enabled AND room_products.room_id=".$info['room_id']."
					ORDER BY product_type, products.product_name","Getting Products for room;")
?>
		<div class='innerbody' id="room_products" style="margin-top: 10px; display:none;">
			<div class="colHeader">Room Products</div>
			<table cellpadding="3" cellspacing="0" style="width:100%;" class="List">
				<tr>
					<th>Product</th>
					<th style="width:10px; white-space:nowrap;">Qty</th>
				</tr>
<?
		$product_type = '';
		$cnt = 0;
		while($row = mysqli_fetch_assoc($products)) {
			if($product_type != $row['product_type']) {
?>
				<tr class="ListHeader">
					<td colspan="5" style="font-weight:bold;">
						<?=$row['product_type']?>
					</td>
				</tr>
<?
				$product_type = $row['product_type'];
			}
?>
				<tr class='<?=($cnt++%2 ? 'ListOdd' : 'ListEven')?>'>
					<td><?=decode($row['product_name'])?></td>
					<td style="text-align:center; vertical-align:middle;"><?=decode($row['quantity'])?></td>
				</tr>
<?		
		}
		if($cnt == 0) {
?>
				<tr class='<?=($cnt++%2 ? 'ListOdd' : 'ListEven')?>'>
					<td colspan="5" style="text-align:center;">
						<em>No products assigned to this room!</em>
					</td>
				</tr>
<?		
		}
?>

			</table>
		</div>


		
		<div class='innerbody' style="margin-top: 10px;">
			<div class="colHeader">Check-Off</div>
			<div style="padding:3px;">
<?
			if($info['checked_datetime'] == '') {
?>
			<div style="font-weight:bold; color:red;">Check off Session if all equipment is setup and functional and Instructor is ready to go. <em>NOTE! This cannot be undone. Please be 100% sure this session is ready to go before checking it off.</em></div>
			<form action="" method="post" id="idForm" onsubmit="" style='padding:0; margin:0;'>
			<table cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td width='55'><input type='checkbox' name='check-off' id='check-off' value='1' style='width:50px; height:50px;' /></td>
					<td style='font-size:12px; font-weight:bold;'>Check-In Session</td>
					<td style='padding-left:5px;'><input type='submit' value='Submit' style='width:100px;'/></td>
				</tr>
			</table>
				<input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
			</form>
			
<?
			} else {
?>
			<div style="">Checked In: <?=pretty_datetime($info['checked_datetime'])?></div>
			<div style="">By: <?=$info['sfirst_name'].' '.$info['slast_name']?> 
<?
		$phone = preg_replace('/(\||\'|\"|\.|\@\,| |\-|\(|\)|\+|\-|[a-zA-Z])+/','',$info['scellnumber']);
		if(strlen($phone) == 11 && $phone[0] == "1") { $phone = substr($phone, 1); }
		if(strlen($phone) == 10 && $info['stext_method'] != '') {
		$msg = 'To: '.$info['sfirst_name'].' '.$info['slast_name'].' -- From: '.$_SESSION['first_name'].' '.$_SESSION['last_name'].' -- Session: '.$info['class_name'].' -- Room: '.$info['room_name'].' -- Building: '.$info['building_name'].' -- Call Back: '.$_SESSION['mobile'];
?>			
			<input type="button" id="text_tech" value="Request Assistance" onclick="text_tech('<?=$BF?>','<?=$_SESSION['user_email']?>','<?=$phone.$info['stext_method']?>','<?=$msg?>','text_tech')" />
<?
		}
?>			
			</div>
<?			
			}
?>
			</div>
		</div>		
<?

	}
?>