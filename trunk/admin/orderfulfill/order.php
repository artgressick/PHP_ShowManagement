<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info,$show_data,$orderinfo;
		$allow_ordering = true;
//		if(date('Y-m-d') < $show_data['lock_requests']) { $allow_ordering = true; }
		$product_list = "";
		if($orderinfo['id'] != '') {
			//Lets grab products ordered first
			$tmp_ordered_products = db_query("SELECT * FROM session_order_items WHERE !deleted AND order_id='".$orderinfo['id']."'","Get all ordered Products");
			$products_ordered = array();
			
			while($row = mysqli_fetch_assoc($tmp_ordered_products)) {
				$products_ordered[$row['timeslot_id']][$row['product_id']] = array('qty'=>$row['quantity'],'approved'=>$row['approved'],'price'=>$row['price'],'setup'=>$row['setup'],'aa'=>$row['added_by_admin']); 
			}
			//now for any custom orders
			$tmp_custom_orders = db_query("SELECT * FROM session_notes WHERE order_id='".$orderinfo['id']."' AND note_type_id=1","Getting other requests");
			$custom_orders = array();
			while($row = mysqli_fetch_assoc($tmp_custom_orders)) {
				$custom_orders[$row['timeslot_id']][$row['producttype_id']] = array('note'=>$row['note']); 
			}
			//Now for the other two fields
			$tmp_notes = db_query("SELECT * FROM session_notes WHERE order_id='".$orderinfo['id']."' AND note_type_id != 1","Getting Notes");
			$order_notes = array();
			while($row = mysqli_fetch_assoc($tmp_notes)) {
				if($row['note_type_id'] == 3) {
					$order_notes[3][$row['timeslot_id']] = array('note'=>$row['note']);
				} else {
					$order_notes[$row['note_type_id']] = array('note'=>$row['note']); 
				}
			}
		}
?>
<form action="" method="post" id="idForm" onsubmit="">
	<table cellpadding="0" cellspacing="0" class="page_info">
		<tr>
			<td class="headleft"><!-- BLANK --></td>
			<td class="headmiddle" style="padding-left:5px;">Session Information<?=($_SESSION['admin_access'] == 1?" (".linkto(array("display"=>"Edit","address"=>"admin/sessions/edit.php?key=".$info['lkey'])).")":"")?></td>
			<td class="headright"><!-- BLANK --></td>
		</tr>
		<tr>
			<td class="body" colspan="3">
				<table cellpadding="0" cellspacing="0" style="width:100%;">
					<tr>
						<td style="width:50%; vertical-align:top;">
							
							<div class="inline"><span class="question">Session:</span> <?=$info['class_name']?> (<?=$info['class_number']?>)</div>
							
							<div class="inline"><span class="question">Session Category/Type:</span> <?=$info['sessioncat_name']?> / <?=$info['sessiontype_name']?></div>
							
							<div class="inline"><span class="question">Speaker:</span> <?=$info['speaker']?></div>
<?
						$status = db_query("SELECT id, order_status AS name FROM order_status ","Get Status");
						if($orderinfo['quote_name'] != '') {
?>							
							<div class="inline"><span class="question">Order Number:</span> <?=$orderinfo['quote_name'].'-'.$orderinfo['revision']?> <span style="color:red;">(<strong>Status:</strong> 
								<?=form_select($status,array('caption'=>'- Order Status-','required'=>'true','nocaption'=>'true','name'=>'status_id','value'=>$orderinfo['status_id']))?>)</span></div>
<?
						} else {
?>
							<strong>Status:</strong> 
								<?=form_select($status,array('caption'=>'- Order Status-','required'=>'true','nocaption'=>'true','name'=>'status_id','value'=>'1'))?>
<?						
						}
?>
							<div class="inline"><span class="question">Billing Party:</span> <?=($info['bill_other']?$info['bill_name'].' (<a href="mailto:'.$info['bill_email'].'">'.$info['bill_email'].'</a>)':'Main Budget')?></div>
<?
						if($info['bill_other']) {
?>
							<div class="inline"><span class="question">Product Total:</span> $<span id="product_charge"></span></div>

							<div class="inline"><span class="question">Setup Charge:</span> $<span id="setup_charge"></span></div>
							
							<div class="inline"><span class="question">Contingency:</span> <input type="text" name="contingency" id="contingency" size="4" maxlength="3" value="<?=(isset($orderinfo['contingency']) ? $orderinfo['contingency'] : '10')?>" onchange="calculate_total_bill();" />% = $<span id="cont_charge"></span></div>
							
							<div class="inline" style="font-size:16px;"><span class="question">Order Total:</span> $<span style="font-weight:bold;" id="total_charge"></span></div>
<?						
							if($orderinfo['quote_name'] != '') {
?>
							<div style="padding-top:20px;"><strong>PDF:</strong>&nbsp;&nbsp;<a href="pdf_order.php?key=<?=$info['lkey']?>">Download Quote</a>&nbsp;&nbsp;&nbsp;<a href="pdf_order.php?key=<?=$info['lkey']?>&action=email">E-mail Quote to Billing Party</a></div>
							<div style="padding-top:5px;"><strong>Excel:</strong>&nbsp;&nbsp;<a href="excel_order.php?key=<?=$info['lkey']?>">Download Quote</a></div>
<?								
							}
						}
?>							
							

						</td>
						<td style="width:50%; vertical-align:top;">
							<div class="colHeader" style="width:100%;">Dates/Times</div>
								<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
									<thead>
										<tr>
											<th>Description</th>
											<th>Date</th>
											<th>Start Time</th>
											<th>End Time</th>
											<th>Room (Building)</th>
										</tr>
									</thead>
									<tbody>
<?
									$datestimes = db_query('SELECT time_slots.*, rooms.room_name, buildings.building_name 
													FROM time_slots 
													JOIN rooms ON time_slots.room_id=rooms.id
													JOIN buildings ON rooms.building_id = buildings.id
													WHERE !time_slots.deleted AND class_id='.$info['id'].' 
													ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
													time_slots.description','Getting Dates and Times');
									$i = 1;
									while($row = mysqli_fetch_assoc($datestimes)) {		
?>							
										<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
											<td class="nocursor"><?=$row['description']?></td>
											<td class="nocursor" style="white-space:nowrap;"><?=pretty_date($row['start_date'])?></td>
											
											<td class="nocursor" style="white-space:nowrap;"><?=pretty_time($row['start_time'])?></td>
											<td class="nocursor" style="white-space:nowrap;"><?=pretty_time($row['end_time'])?></td>
											<td class="nocursor"><?=$row['room_name']?> (<?=$row['building_name']?>)</td>
										</tr>
<?
										$i++;
									}
?>			
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?
	mysqli_data_seek($datestimes,0);
	while($row = mysqli_fetch_assoc($datestimes)) {
?>
	<table cellpadding="0" cellspacing="0" class="page_info">
		<tr>
			<td class="headleft"><!-- BLANK --></td>
			<td class="headmiddle" style="width:10px;"><?=linkto(array('address'=>'#','img'=>'add.png','display'=>"Add products to order",'extra'=>'onclick="window.open(\'add_popup.php?key='.$info['lkey'].'&ts='.$row['id'].'\', \'add_porducts\', 
\'location=no, status=1, height=700, width=600, resizable=yes, scrollbars=yes, toolbar=no, menubar=no\')"'))?></td>
			<td class="headmiddle" style="padding-left:5px;"><?=date('l, F j Y',strtotime($row['start_date']))?> from <?=pretty_time($row['start_time'])?> to <?=pretty_time($row['end_time'])?><?=(strlen($row['description']) > 2 ?' ('.$row['description'].')':'')?></td>
			<td class="headright"><!-- BLANK --></td>
		</tr>
		<tr>
			<td class="body" colspan="4">
<?
	$roominfo = db_query("SELECT rooms.* FROM rooms WHERE rooms.id=".$row['room_id'],"Getting Room Data",1)
?>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="subsection" style="width:49%; vertical-align:top;">
							<div class="colHeader3" style="width:100%; ">Room Information<?=($_SESSION['admin_access'] == 1?" (".linkto(array("display"=>"Edit","address"=>"admin/rooms/edit.php?key=".$roominfo['lkey'])).")":"")?></div>
							<div style="padding:5px;">
								<div class="inline"><span class="question">Room Name:</span> <?=$roominfo['room_name']?> <?=(strlen($roominfo['room_number']) > 2 ?' ('.$roominfo['room_number'].')':'')?></div>
							
								<div class="inline"><span class="question">Description:</span> <?=$roominfo['description']?></div>
								
								<div class="inline"><span class="question">High Speed Internet Access:</span> <?=($roominfo['internet_access']?'YES':'NO')?></div>
								
							</div>
						</td>
						<td style="width:2%"></td>
						<td class="subsection" style="width:49%; vertical-align:top;">
							<div class="colHeader3" style="width:100%;">Room Standard Products<?=($_SESSION['admin_access'] == 1?" (".linkto(array("display"=>"Edit","address"=>"admin/rooms/products.php?key=".$roominfo['lkey'])).")":"")?></div>
							<div style="padding:5px;">
<?
							$room_products = db_query("SELECT product_types.id, product_types.product_type, 
													GROUP_CONCAT(CONCAT(IF(room_products.quantity>1,
														CONCAT('(',room_products.quantity,' x) '),''),products.common_name) 
													ORDER BY products.common_name SEPARATOR ', ') AS products
												FROM room_products
												JOIN products ON room_products.product_id=products.id
												JOIN product_types ON products.producttype_id=product_types.id
												WHERE products.enabled AND !room_products.deleted AND !products.deleted 
													AND room_products.room_id='".$row['room_id']."'
												GROUP BY product_types.id
												ORDER BY product_types.product_type","Getting Products");
												
							if(mysqli_num_rows($room_products) > 0) {
?>
								<ul>
<?
								while($rp = mysqli_fetch_assoc($room_products)) {
								
?>
									<div class="inline"><span class="question"><?=$rp['product_type']?>:</span> <?=(strlen($rp['products']) > 200 ? substr($rp['products'],0,200).'... '.linkto(array('address'=>'#','display'=>"(View ALL)",'extra'=>'onclick="window.open(\'view_more.php?key='.$roominfo['lkey'].'&pt='.$rp['id'].'\', \'View Products\', \'location=no, status=1, height=700, width=600, resizable=yes, scrollbars=yes, toolbar=no, menubar=no\')"','style'=>'color:red;')):$rp['products'])?></div>
<?							
								}
?>
								</ul>
<?
							} else {
?>
								<div><em>No Products Assigned to this room</em></div>
<?							
							}
							if(strlen($roominfo['notes']) > 2) {
?>
								<div class="legend" style="padding:5px;"><?=nl2br($roominfo['notes'])?></div>
<?							
							}
?>							
							</div>						
						</td>
					</tr>
				</table>
				
<?
			//Lets get all the product categories first
			$producttypes = db_query("SELECT product_types.*, sp.allowcustom, sp.id AS sp_id
									FROM product_types
									LEFT JOIN sessiontype_producttypes AS sp ON product_types.id=sp.producttype_id 
										AND sp.sessiontype_id='".$info['sessiontype_id']."'
									ORDER BY product_type
										","getting product types");

?>
				<table cellpadding="0" cellspacing="0" style="width:100%; margin-top:15px;">
					<tr>
<?
				$ct = 1;
				while($pt = mysqli_fetch_assoc($producttypes)) {
					if($ct > 2) {
?>
					</tr>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr>
<?					
						$ct = 1;
					}
?>
						<td style="width:49%; vertical-align:top;" class="subsection">
							<div class="colHeader3" style="width:100%;"><?=$pt['product_type']?> Products</div>	
							<div style="padding:5px;">
<?
		$products = db_query("SELECT products.id, products.common_name, products.price, products.setup_fee, products.needs_quantity, product_types.product_type, request_note
								FROM products
								JOIN product_types ON products.producttype_id=product_types.id
								JOIN product_show ON product_show.product_id=products.id
								WHERE !products.deleted AND products.enabled AND !product_types.deleted AND product_types.enabled 
									AND product_show.show_id='".$_SESSION['show_id']."'
									AND ((products.id IN (SELECT product_sessiontypes.product_id FROM product_sessiontypes 
											WHERE product_sessiontypes.sessiontype_id='".$info['sessiontype_id']."') 
									AND products.id NOT IN (SELECT product_id 
										FROM room_products
										JOIN products AS P ON room_products.product_id=P.id
										WHERE !room_products.deleted AND !P.needs_quantity)) 
									OR products.id IN (SELECT product_id FROM session_order_items WHERE !deleted AND order_id='".$orderinfo['id']."' 
										AND timeslot_id='".$row['id']."'))
									AND products.producttype_id='".$pt['id']."'
								ORDER BY product_types.product_type, products.common_name
							","Get ".$pt['product_type']." Products");

?>
								<table class="List" cellpadding="0" cellspacing="0" style="width:100%; margin-bottom:10px;">
									<tr>
										<th style="width:10px;"></th>
										<th style="width:10px;">Qty</th>
										<th>Product</th>
<?								if($info['bill_other']) { ?>
										<th>Price</th>
										<th>Setup</th>
<?								}	?>
										<th style="width:10px;">Approved</th>
									</tr>
									<tbody id="ptable_<?=$row['id']?>_<?=$pt['id']?>">
<?
									$i = 1;
									while($p = mysqli_fetch_assoc($products)) {	
										$product_list .= $row['id']."_".$p['id'].",";
// Now lets clean up some of the code below so we can 1 make sure to get rid of all the notices and 2 just make it easier to read

//Lets start with the first field the check box
$product_options = "";
if(isset($products_ordered[$row['id']][$p['id']])) { $product_options .= ' checked="checked"'; }

//Now for the quantity field
$quantity_options = "";
if($p['needs_quantity']) {
	$quantity_options = ' type="text" size="3" maxlength="3"';
	if(isset($products_ordered[$row['id']][$p['id']])) { 
		$quantity_options .= ' value="'.$products_ordered[$row['id']][$p['id']]['qty'].'"';
	} else {
		$quantity_options .= ' disabled="disabled"';
	}
	if($info['bill_other']) { $quantity_options .= ' onchange="calculate_total_bill();"'; }
	
} else {
	$quantity_options = ' type="hidden"';
	if(isset($products_ordered[$row['id']][$p['id']])) { $quantity_options .= ' value="1"'; }
}
if($info['bill_other']) {
	//Now for the price field
	$price_options = "";
	if(isset($products_ordered[$row['id']][$p['id']])) {
		$price_options .= ' value="'.$products_ordered[$row['id']][$p['id']]['price'].'"';
	} else {
		$price_options .= ' value="'.$p['price'].'" disabled="disabled"';
	}
	//Now for the setup field
	$setup_options = "";
	if(isset($products_ordered[$row['id']][$p['id']])) {
		$setup_options .= ' value="'.$products_ordered[$row['id']][$p['id']]['setup'].'"';
	} else {
		$setup_options .= ' value="'.$p['setup_fee'].'" disabled="disabled"';
	}
}

//Now for the approved box
$approved_options = "";
if(isset($products_ordered[$row['id']][$p['id']])) {
	if($products_ordered[$row['id']][$p['id']]['approved']) { $approved_options .= ' checked="checked"'; }
} else {
	$approved_options .= ' disabled="disabled"';
}
if($info['bill_other']) { $approved_options .= ' onchange="calculate_total_bill();"'; }
?>							
										<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
											<td class="nocursor">
												<input type="checkbox" name="product_ids_<?=$row['id']?>[]" id="product_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p['id']?>"<?=$product_options?> onchange="check_product('<?=$row['id']?>_<?=$p['id']?>');" />
											</td>
											<td class="nocursor" style="white-space:nowrap;">
												<input name="qty_<?=$row['id']?>_<?=$p['id']?>" id="qty_<?=$row['id']?>_<?=$p['id']?>"<?=$quantity_options?> />
											</td>
											<td class="nocursor"><label for="product_<?=$row['id']?>_<?=$p['id']?>"><?=$p['common_name']?></label></td>
<?	if($info['bill_other']) { ?>
											<td class="nocursor" style="white-space:nowrap;">
												$<input type="text" name="price_<?=$row['id']?>_<?=$p['id']?>" id="price_<?=$row['id']?>_<?=$p['id']?>" size="6" maxlength="13" onchange="calculate_total_bill();"<?=$price_options?> />
											</td>
											<td class="nocursor" style="white-space:nowrap;">
												$<input type="text" name="setup_<?=$row['id']?>_<?=$p['id']?>" id="setup_<?=$row['id']?>_<?=$p['id']?>" size="6" maxlength="13" onchange="calculate_total_bill();"<?=$setup_options?> />
											</td>
<?	} ?>											
											<td class="nocursor" style="white-space:nowrap;">
												<input type="checkbox" name="approved_<?=$row['id']?>_<?=$p['id']?>" id="approved_<?=$row['id']?>_<?=$p['id']?>" value="1" <?=$approved_options?> />
											</td>
										</tr>
<?
										$i++;
									}
?>
									</tbody>
								</table>
<?								
							
							if($pt['allowcustom']) {
?>
								<?=form_textarea(array('caption'=>'Presenter Custom Request','name'=>'custom_'.$row['id'].'_'.$pt['id'],'cols'=>'50','rows'=>'5','style'=>'width:100%;','value'=>(isset($custom_orders[$row['id']][$pt['id']])?$custom_orders[$row['id']][$pt['id']]['note']:'')))?>
								
<?						
							}
							// Questions Section
							$tmp_question = db_query("SELECT pt_questions.*, answers.id AS answer_id, answers.answer
								FROM pt_questions 
								LEFT JOIN answers ON pt_questions.id=answers.question_id AND answers.timeslot_id='".$row['id']."' AND answers.order_id='".$orderinfo['id']."'
								WHERE !pt_questions.deleted AND pt_questions.show_id='".$_SESSION['show_id']."' 
									AND pt_questions.sessiontype_id='".$info['sessiontype_id']."' AND pt_questions.producttype_id='".$pt['id']."'
								ORDER BY pt_questions.sort_order, pt_questions.question
							
							",'Getting Question');
		$t=1;
		while($question = mysqli_fetch_assoc($tmp_question)) {
			if($question['fieldtype_id'] == 1) {
?>
				<div class="colHeader4" style="margin-top:10px;"><?=$question['question']?></div>
<?				
			} else {
?>
				<div style='font-weight: bold; margin-top:10px; width:460px;'><?=$t++.". ".$question['question'].($question['required'] ? ' <span class="FormRequired">(Required)</span>' : '')?></div>
<?			
				if($question['fieldtype_id'] == 2 || $question['fieldtype_id'] == 3) {
?>
				<div style='padding-left:12px;padding-top:3px;'><input type='text' name='answer_<?=$row['id']?>_<?=$question['id']?>' id='answer_<?=$row['id']?>_<?=$question['id']?>' style='width: 400px;' value="<?=$question['answer']?>" /></div><?			
				} else if($question['fieldtype_id'] == 4) {
?>
				<div style='padding-left:12px;padding-top:3px;width:460px;'><textarea name='answer_<?=$row['id']?>_<?=$question['id']?>' id='answer_<?=$row['id']?>_<?=$question['id']?>' cols='100' rows='5' style="width:400px;"><?=$question['answer']?></textarea></div><?			
				} else if($question['fieldtype_id'] == 5) {
?>
				<div style='padding-left:12px;padding-top:3px;'><select name='answer_<?=$row['id']?>_<?=$question['id']?>' id='answer_<?=$row['id']?>_<?=$question['ID']?>'>
						<option value=''>-Select Answer-</option>
<?
					$tmp_options = explode('|||',$question['options']);
					$j = 0;
					foreach($tmp_options as $v) {
?>
					<option value='<?=$j?>'<?=($v == $question['answer']?" selected='selected'":'')?>><?=$v?></option><?
						$j++;
					}
?>						
					</select></div>
<?
				} else if($question['fieldtype_id'] == 6) {
					$tp_answer = explode(', ',$question['answer']);
					$tmp_options = explode('|||',$question['options']);
					$j = 0;
					foreach($tmp_options as $v) {
?>
				<label for='<?=$question['id']?>-<?=$j?>' style='padding-left: 10px;'><input type='checkbox' name='answer_<?=$row['id']?>_<?=$question['id']?>[]' id='answer_<?=$row['id']?>_<?=$question['id']?>-<?=$j?>' value='<?=$j?>' <?=(in_array($v,$tp_answer)?' checked="checked"':'')?>> <?=$v?></label><?
						$j++;
					}
				} else if($question['fieldtype_id'] == 7) {
					$tmp_options = explode('|||',$question['options']);
					$j = 0;
					foreach($tmp_options as $v) {
?>				<label for='<?=$question['id']?>-<?=$j?>' style='padding-left: 10px;'><input type='radio' name='answer_<?=$row['id']?>_<?=$question['id']?>[]' id='answer_<?=$row['id']?>_<?=$question['id']?>-<?=$j?>' value='<?=$j?>' <?=($v == $question['answer']?" checked='checked'":'')?>> <?=$v?></label><?
						$j++;
					}
				}
?>
<?
			}
		}
?>							
							</div>
						</td>
<?
					if($ct == 1) {
?>
						<td style="width:2%;">&nbsp;</td>
<?					
					}
					$ct++;
				}
				if($ct == 3) {
?>
					</tr>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr>
<?				
				}
?>
						<td style="width:49%; vertical-align:top;" class="subsection">
							<div class="colHeader3" style="width:100%;">Presenter Bringing Own</div>	
							<div style="padding:5px;">
							<div>Please describe any additional equipment you are planning to provide for yourself or your participants (i.e., digital cameras, iPods, GPS units, other computer peripherals or AT apparatus, specific software titles).  This includes presenter laptops if you intend to use your own computer instead of our presentation station.</div>
							<?=form_textarea(array('caption'=>'Bringing Own','nocaption'=>'true','name'=>'itemsbeingbrought'.$row['id'],'cols'=>'50','rows'=>'5','style'=>'width:100%;','value'=>(isset($order_notes[3][$row['id']]) ? $order_notes[3][$row['id']]['note']:''),'extra'=>(!$allow_ordering?'disabled="disabled"':'')))?>

						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?	
	}
?>
	<table cellpadding="0" cellspacing="0" class="page_info">
		<tr>
			<td class="headleft"><!-- BLANK --></td>
			<td class="headmiddle" style="padding-left:5px;">Other Information</td>
			<td class="headright"><!-- BLANK --></td>
		</tr>
		<tr>
			<td class="body" colspan="3">
				<?=form_textarea(array('caption'=>'Presenter Order Notes','name'=>'ordernote','cols'=>'50','rows'=>'5','style'=>'width:100%;','value'=>(isset($order_notes[2]) ? $order_notes[2]['note']:''),'extra'=>(!$allow_ordering?'disabled="disabled"':'')))?>
			</td>
		</tr>
	</table>
<?
	if($product_list != "") { $product_list = substr($product_list,0,-1); }
?>
	<div class='FormButtons'>
		<?=form_button(array('type'=>'submit','name'=>'Save','value'=>'Save Request'))?>
		<input type="hidden" name="query_string" value="<?=$_SERVER['QUERY_STRING']?>" />
		<input type="hidden" name="order_id" value="<?=($orderinfo['id']!=''?$orderinfo['id']:'')?>" />
		<input type="hidden" id="product_ids" value="<?=$product_list?>" />
	</div>
</form>
<?
	}
?>