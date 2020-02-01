<?php
	include('_controller.php');

	function sitm() {
		global $BF,$info;

?>
		<div class='innerbody'>
			<div class="colHeader">Room Information</div>
		
			<table cellpadding="0" cellspacing="0" style="width:100%;">
				<tr>
					<td style="padding:3px; width:50%; vertical-align:top;">
						<?=form_text(array('caption'=>'Room Name','value'=>$info['room_name'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Room Description','value'=>$info['description'],'display'=>'true'))?>
<?					
						$building = db_query("SELECT id, building_name as name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Getting Buildings");
?>
						<?=form_select($building,array('caption'=>'Building','value'=>$info['building_id'],'display'=>'true'))?>

					</td>
					<td style="padding:3px; width:50%; vertical-align:top;">
<?					
						$platforms = db_query("SELECT id, platform_name as name FROM platforms ORDER BY id","Getting platforms");
?>
						<?=form_select($platforms,array('caption'=>'Platform','value'=>$info['platform_id'],'display'=>'true'))?>

						<?=form_text(array('caption'=>'Has Internet Access','value'=>(!$info['internet_access']?'NO':'Yes'),'display'=>'true'))?>
						<div style="padding-top:20px; text-align:right;"><input type="button" id="show_btn" value="Show More Details" onclick="show_more();" /></div>
					</td>
				</tr>
				<tr id="more_room_data" style="display:none;">
					<td style="padding:3px; width:50%; vertical-align:top;">
						<?=form_text(array('caption'=>'Capacity','value'=>$info['capacity'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Square Footage','value'=>$info['square_feet'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Dimensions','value'=>$info['dimensions'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Public Location','value'=>$info['public_location'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Default Setup','value'=>$info['default_setup'],'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Category','value'=>$info['category'],'display'=>'true'))?>
					</td>
					<td style="padding:3px; width:50%; vertical-align:top;">
						<?=form_text(array('caption'=>'Move-in Date','value'=>date('Y-m-d',strtotime($info['move_in'])),'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Move-in Time','value'=>date('g:i a',strtotime($info['move_in'])),'display'=>'true'))?>
					
						<?=form_text(array('caption'=>'Move-out Date','value'=>date('Y-m-d',strtotime($info['move_out'])),'display'=>'true'))?>
						
						<?=form_text(array('caption'=>'Move-out Time','value'=>date('g:i a',strtotime($info['move_out'])),'display'=>'true'))?>
					</td>
				</tr>
			</table>
		</div>
<?
		$files = db_query("SELECT * FROM room_files WHERE room_id=".$info['id'],"Getting Files");
		if(mysqli_num_rows($files) > 0) {
?>
		<div style="margin-top:10px;" class="innerbody">
			<div class="colHeader">Diagram(s)</div>
<?	
			while($row = mysqli_fetch_assoc($files)) {
?>			
				<div style='padding:5px 5px;'><a href='<?=$BF?>files/rooms/<?=$row['file_name']?>'><?=$row['file_name']?></a></div>

<?
			}
?>
			</div>
<?	
		}


	$products = db_query("SELECT room_products.*, CONCAT('<strong>',products.product_name,'<strong> <span style=\"font-size:10px;\">(',products.common_name,')</span>') AS product_name, product_types.product_type, room_products.quantity 
					FROM room_products
					JOIN products ON room_products.product_id=products.id
					JOIN product_types ON products.producttype_id = product_types.id
					WHERE !room_products.deleted AND products.enabled AND product_types.enabled AND room_products.room_id=".$info['id']."
					ORDER BY product_type, products.product_name","Getting Products for room;")
?>
		<div class='innerbody' style="margin-top: 10px;">
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
			<form action="" method="post" id="idForm" onsubmit="" style='padding:0; margin:0;'>
			<div style="font-weight:bold; color:red;">I certify that this room has been reviewed and all products listed above is setup and functioning properly. <em>NOTE! This cannot be undone. Please be 100% sure this room is ready to go before checking it off.</em></div>
			<table cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td width='55'><input type='checkbox' name='check-off' id='check-off' value='1' style='width:50px; height:50px;' /></td>
					<td style='font-size:12px; font-weight:bold;'>Check-In Room</td>
					<td style='padding-left:5px;'><input type='submit' value='Submit' style='width:100px;'/></td>
				</tr>
			</table>
				<input type="hidden" name="key" value="<?=$_REQUEST['key']?>" />
			</form>
<?
			} else {
?>
			<div style="">Checked In: <?=pretty_datetime($info['checked_datetime'])?></div>
			<div style="">By: <?=$info['first_name'].' '.$info['last_name']?> 
<?
		$phone = preg_replace('/(\||\'|\"|\.|\@\,| |\-|\(|\)|\+|\-|[a-zA-Z])+/','',$info['cellnumber']);
		if(strlen($phone) == 11 && $phone[0] == "1") { $phone = substr($phone, 1); }
		if(strlen($phone) == 10 && $info['text_method'] != '') {
		$msg = 'To: '.$info['first_name'].' '.$info['last_name'].' -- From: '.$_SESSION['first_name'].' '.$_SESSION['last_name'].' -- Room: '.$info['room_name'].' -- Building: '.$info['building_name'].' -- Call Back: '.$_SESSION['mobile'];
?>			
			<input type="button" id="text_tech" value="Request Assistance" onclick="text_tech('<?=$BF?>','<?=$_SESSION['user_email']?>','<?=$phone.$info['text_method']?>','<?=$msg?>','text_tech')" />
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