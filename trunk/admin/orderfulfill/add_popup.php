<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$info,$orderinfo;

		$datestimes = db_query('SELECT time_slots.id
						FROM time_slots 
						JOIN rooms ON time_slots.room_id=rooms.id
						JOIN buildings ON rooms.building_id = buildings.id
						WHERE !time_slots.deleted AND class_id='.$info['id'].' 
						ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
						time_slots.description','Getting Dates and Times');

		$datestime = db_query('SELECT time_slots.*, rooms.room_name, buildings.building_name
						FROM time_slots 
						JOIN rooms ON time_slots.room_id=rooms.id
						JOIN buildings ON rooms.building_id = buildings.id
						WHERE !time_slots.deleted AND class_id='.$info['id'].' AND time_slots.id="'.$_REQUEST['ts'].'"
						ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
						time_slots.description','Getting Date and Time',1);

		$products = db_query("SELECT products.id, products.common_name, products.price, products.setup_fee, products.needs_quantity, product_types.product_type
								FROM products
								JOIN product_types ON products.producttype_id=product_types.id
								JOIN product_show ON product_show.product_id=products.id
								WHERE !products.deleted AND products.enabled AND !product_types.deleted AND product_types.enabled 
									AND product_show.show_id='".$_SESSION['show_id']."'
									AND products.id NOT IN (SELECT product_id 
										FROM room_products
										JOIN products AS P ON room_products.product_id=P.id
										WHERE !room_products.deleted AND !P.needs_quantity AND room_products.room_id='".$datestime['room_id']."')
								ORDER BY products.common_name, product_types.product_type, products.common_name
							","Get All Products");
?>
	<form action="" method="post" id="idForm" onsubmit="">
		<table class="nav_bar" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
			<tr>
				<td class="nbmiddle" style="">Select Products for Order</td>
			</tr>
		</table>
		<div style="padding:5px;">
			<div class="inline"><span class="question">Session:</span> <?=$info['class_name']?> (<?=$info['class_number']?>)</div>
			
			<div class="inline"><span class="question">Session Category/Type:</span> <?=$info['sessioncat_name']?> / <?=$info['sessiontype_name']?></div>
			
			<div class="inline"><span class="question">Speaker:</span> <?=$info['speaker']?></div>
			
			<div class="inline"><span class="question">Billing Party:</span> <?=($info['bill_other']?$info['bill_name']:'Main Budget')?></div>
			
			<div class="inline"><span class="question">Date and Time: </span> <?=date('l, F j Y',strtotime($datestime['start_date']))?> from <?=pretty_time($datestime['start_time'])?> to <?=pretty_time($datestime['end_time'])?><?=(strlen($datestime['description']) > 2 ?' ('.$datestime['description'].')':'')?></div>
		</div>
		<div style="padding:5px;">
		<table class="List" cellpadding="0" cellspacing="0" style="width:100%;">
			<tr>
				<th style="width:10px;"></th>
				<th style="width:10px;">Qty</th>
				<th>Product</th>
				<th>Product Type</th>
<?
			if($info['bill_other']) {
?>
				<th style="width:10px;">Price</th>
				<th style="width:10px;">Setup</th>
<?
			}
			if(mysqli_num_rows($datestimes) > 1) {
?>				
				<th style="width:10px;">Apply to All Times</th>
<?
			}
?>
			</tr>
<?
			$i = 1;
			while($row = mysqli_fetch_assoc($products)) {		
?>							
			<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
				<td class="nocursor"><input type="checkbox" name="products[]" id="product_<?=$row['id']?>" value="<?=$row['id']?>" onchange="check_product('<?=$row['id']?>')" /></td>
				<td>
<?
				if($row['needs_quantity']) {
?>
					<input type="text" name="qty_<?=$row['id']?>" id="qty_<?=$row['id']?>" size="3" maxlength="3" value="" disabled="disabled" />
<?
				} else {
?>
					<input type="hidden" name="qty_<?=$row['id']?>" id="qty_<?=$row['id']?>" value="" />
<?				
				}
?>
				</td>
				<td class="nocursor"><label for="product_<?=$row['id']?>"><?=$row['common_name']?></label></td>
				<td class="nocursor"><?=$row['product_type']?></td>
<?
			if($info['bill_other']) {
?>
				<td class="nocursor"><input type="text" name="price_<?=$row['id']?>" id="price_<?=$row['id']?>" size="6" maxlength="13" value="<?=$row['price']?>"  disabled="disabled" /></td>
				<td class="nocursor"><input type="text" name="setup_<?=$row['id']?>" id="setup_<?=$row['id']?>" size="6" maxlength="13" value="<?=$row['setup_fee']?>"  disabled="disabled" /></td>
<?
			}
			if(mysqli_num_rows($datestimes) > 1) {
?>				
				<td class="nocursor"><input type="checkbox" name="applyall_<?=$row['id']?>" id="applyall_<?=$row['id']?>" value="1" disabled="disabled" /></td>
<?
			}
?>
			</tr>
<?
				$i++;
			}
?>
		</table>
		</div>
		<div style="padding:5px; text-align:center;">
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Add','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'add\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'submit','value'=>'Add and Close','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'addclose\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'button','value'=>'Cancel and Close','extra'=>'onclick="window.close();"'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'moveTo'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'ts','value'=>$_REQUEST['ts']))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'order_id','value'=>($orderinfo['id']!=''?$orderinfo['id']:'')))?>
			</div>
		</div>
	</form>
<?
	}
?>