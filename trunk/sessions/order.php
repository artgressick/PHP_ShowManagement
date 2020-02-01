<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info,$show_data,$orderinfo;
		
		$ignorelock = db_query("SELECT id FROM sessiontypes WHERE id='".$info['sessiontype_id']."' AND ignore_lock_date","Get Session Types for lock ignore",1);
		
		
		$allow_ordering = false;
		if(date('Y-m-d') < $show_data['lock_requests']) { $allow_ordering = true; }
		if($_SESSION['user_id'] != 1 || $ignorelock['id'] == $info['sessiontype_id']) { $allow_ordering = true; }
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

//	echo base64_encode('session_id='.$info['id'].'&key='.date('Ymd'));
//	$showman_url = "http://showman.techitweb.com/sessions/order.php?d=".base64_encode('session_id='.$info['id'].'&key='.date('Ymd'));
//	echo $showman_url;
	if(!$_SESSION['auto_logged']) {
?>
	<table cellpadding="0" cellspacing="0" class="tabs">
		<tr>
			<th class="tab" onclick="location.href='view.php?key=<?=$info['lkey']?>';">Information</th><th class="space"><!-- BLANK --></th>
			<th class="current" onclick="location.href='order.php?key=<?=$info['lkey']?>';">Session Products</th><th class="space"><!-- BLANK --></th>
		</tr>
	</table>
<?
	}
?>
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
							
							<div class="inline"><span class="question">Session:</span> <?=$info['class_name']?> <?=($info['class_number']!=''?'('.$info['class_number'].')':'')?></div>
							
							<div class="inline"><span class="question">Session Category/Type:</span> <?=$info['sessioncat_name']?> / <?=$info['sessiontype_name']?></div>
							
							<div class="inline"><span class="question">Speaker:</span> <?=$info['speaker']?></div>
							
<?
						if($orderinfo['quote_name'] != '') {
?>							
							<div class="inline"><span class="question">Order Number:</span> <?=$orderinfo['quote_name'].'-'.$orderinfo['revision']?> <span style="color:red;">(<strong>Status:</strong> <span id="orig_status"><?=$orderinfo['order_status']?></span><span style="display:none;" id="pend_status">Pending</span>)</span><input type="hidden" id="status_id" value="<?=$orderinfo['status_id']?>" /><?=($_SESSION['admin_access'] == 1?" (".linkto(array("display"=>"Fulfill Order","address"=>"admin/orderfulfill/order.php?key=".$info['lkey'])).")":"")?></div>
<?
						}
?>

							<div class="inline"><span class="question">Billing Party:</span> <?=($info['bill_other']?$info['bill_name']:'Main Budget')?></div>

<?
						if($info['bill_other']) {
?>
							<div class="inline"><span class="question">Product Total:</span> $<span id="product_charge"></span></div>

							<div class="inline"><span class="question">Setup Charge:</span> $<span id="setup_charge"></span></div>
							
							<div class="inline"><span class="question">Contingency:</span> <span id="contingency"><?=(isset($orderinfo['contingency']) ? $orderinfo['contingency'] : '10')?></span>% = $<span id="cont_charge"></span></div>
							
							<div class="inline" style="font-size:16px;"><span class="question">Order Total:</span> $<span style="font-weight:bold;" id="total_charge"></span><span id="total_disclaimer" style="<?=($orderinfo['status_id']==2?'display:none; ':'')?>color:red;"> (Estimate Only)</span></div>
<?						
						}
?>							

						</td>
						<td class="date_time_box" style="width:50%; vertical-align:top;">
							<div class="half_colHeader">Dates/Times</div>
							<table cellpadding="0" cellspacing="0" class="List half_width">
								<thead>
									<tr>
										<th>Description</th>
										<th>Date</th>
										<th>Start Time</th>
										<th>End Time</th>
<?							if($_SESSION['user_id'] != 1 || date('Y-m-d') >= $show_data['show_room_data'] || $info['ignorehiddenroom']) {?>
										<th>Room (Building)</th>
<?							} ?>
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
<?							if($_SESSION['user_id'] != 1 || date('Y-m-d') >= $show_data['show_room_data'] || $info['ignorehiddenroom']) {?>
										<td class="nocursor"><?=$row['room_name']?> (<?=$row['building_name']?>)</td>
<?								} ?>
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
<form action="" method="post" id="idForm" onsubmit="return error_check();">
<?
	mysqli_data_seek($datestimes,0);
	while($row = mysqli_fetch_assoc($datestimes)) {
?>
	<table cellpadding="0" cellspacing="0" class="page_info">
		<tr>
			<td class="headleft"><!-- BLANK --></td>
			<td class="headmiddle" style="padding-left:5px;"><?=date('l, F j Y',strtotime($row['start_date']))?> from <?=pretty_time($row['start_time'])?> to <?=pretty_time($row['end_time'])?><?=(strlen($row['description']) > 2 ?' ('.$row['description'].')':'')?></td>
			<td class="headright"><!-- BLANK --></td>
		</tr>
		<tr>
			<td class="body" colspan="3">
<?
	$roominfo = db_query("SELECT rooms.*, buildings.building_name
							FROM rooms 
							JOIN buildings ON rooms.building_id=buildings.id
							WHERE rooms.id=".$row['room_id'],"Getting Room Data",1)
?>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="subsection" colspan='3' style="vertical-align:top; width:100%;">
							<div class="colHeader3" style="width:100%; ">Standard Room Set</div>
							<table cellpadding="0" cellspacing="0" style="width:100%;">
								<tr>
									<td style="vertical-align:top; width:49%;padding:5px;">
										<div class="inline"><span class="question">Room Name (Building):</span> 
<?
									if($_SESSION['user_id'] != 1 || date('Y-m-d') >= $show_data['show_room_data'] || $info['ignorehiddenroom']) { 
?>
											<?=$roominfo['room_name']?> (<?=$roominfo['building_name']?>)
<?
									} else {
?>
											<strong>TBD Mid-May</strong>
<?						
									}
?>								
										</div>
<?
									if($_SESSION['user_id'] != 1 || date('Y-m-d') >= $show_data['show_room_data'] || $info['ignorehiddenroom']) { 
										if($row['room_area'] != "") {
?>
											<div class="inline"><span class="question">Room Area / Table:</span> <?=$row['room_area']?></div>
<?
										}
									}
									if(strlen($roominfo['notes']) > 2) {
?>
										<div class="legend" style="padding:5px;"><?=nl2br($roominfo['notes'])?></div>
<?							
									}
?>
<?
								//PDF SECTION
								$pdfs = db_query("SELECT * FROM room_files WHERE room_id='".$row['room_id']."' ORDER BY file_name","Get Room PDFs");
								if(mysqli_num_rows($pdfs) > 0) {
									$pdfcnt=0;
?>
									<table cellpadding="0" cellspacing="0" style="width:100%;">

<?
									while($pdfrow = mysqli_fetch_assoc($pdfs)) {
										if($pdfcnt++ == 0) {
?>
										<tr>
											<td style="font-weight:bold; white-space:nowrap; width:50px; padding-right:10px;">Room Layout PDF(s):</td>
<?										
										} else {
?>
										<tr>
											<td>&nbsp;</td>
<?										
										}		
?>											
											<td style="width:20px; text-align:center; vertical-align:middle"><a href="<?=$BF?>files/rooms/<?=$pdfrow['file_name']?>" target='_blank'><img src='<?=$BF?>images/pdficon.jpg' alt="PDF" /></a></td>
											<td style="padding:5px;"><a href="<?=$BF?>files/rooms/<?=$pdfrow['file_name']?>" target='_blank'><?=$pdfrow['file_name']?></a></td>
										</tr>
<?									
									}
?>
										<tr>
											<td style="font-style:italic; padding-top:10px; font-size:11px;" colspan="3">* Requires <a href='http://get.adobe.com/reader/' target='_blank'>Adobe&#174; Acrobat Reader</a> to View</td>
										</tr>
									</table>
<?								
								}
?>

								
									</td>
									<td style="width:2%"></td>
									<td style="width:49%; vertical-align:top; padding:5px;">
<?
									$room_products = db_query("SELECT product_types.id, product_types.product_type, 
															GROUP_CONCAT(CONCAT(IF(room_products.quantity>1,
																CONCAT('(',room_products.quantity,' x) '),''),products.product_name) 
															ORDER BY products.product_name SEPARATOR ', ') AS products
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
											<div class="inline"><span class="question"><?=$rp['product_type']?>:</span> <?=(strlen($rp['products']) > 200 ? substr($rp['products'],0,200).'... '.linkto(array('address'=>'#','display'=>"(View ALL)",'extra'=>'onclick="show_more_products(\''.$roominfo['lkey'].'\',\''.$rp['id'].'\')"','style'=>'color:red;'))

											
											
											:$rp['products'])?></div>
<?							
										}
?>
										</ul>
<?
									} else {
?>
										<div><em>No Items Assigned to this room</em></div>
<?							
									}
?>							
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				
				<div class="full_colHeader" style="margin:10px 0;">Additional Requests <?=(!$allow_ordering?' (<span style="color:red;">Ordering Closed</span>)':'')?></div>
<?
			//Lets get all the product categories first
			$producttypes = db_query("SELECT product_types.*, sp.allowcustom, sp.id AS sp_id
									FROM product_types
									JOIN sessiontype_producttypes AS sp ON product_types.id=sp.producttype_id 
										AND sp.sessiontype_id='".$info['sessiontype_id']."'
									WHERE !product_types.deleted AND product_types.enabled
										 AND ((SELECT COUNT(products.id)
												FROM products
												JOIN product_show ON product_show.product_id=products.id
												WHERE !products.deleted AND products.enabled 
													AND product_show.show_id='".$_SESSION['show_id']."'
													AND ((products.id IN (SELECT product_sessiontypes.product_id FROM product_sessiontypes 
															WHERE product_sessiontypes.sessiontype_id='".$info['sessiontype_id']."') 
													AND products.id NOT IN (SELECT product_id 
														FROM room_products
														JOIN products AS P ON room_products.product_id=P.id
														WHERE !room_products.deleted AND !P.needs_quantity)) 
													OR products.id IN (SELECT product_id FROM session_order_items WHERE !deleted AND order_id='".$orderinfo['id']."' 
														AND timeslot_id='".$row['id']."'))
													AND products.producttype_id=product_types.id) > 0 
												OR sp.allowcustom OR (SELECT COUNT(pt_questions.id) FROM
														pt_questions WHERE !pt_questions.deleted AND pt_questions.producttype_id=product_types.id 
														AND pt_questions.sessiontype_id='".$info['sessiontype_id']."') > 0)
									ORDER BY product_type
										","getting product types");

?>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
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
							<div class="colHeader3" style="width:100%;"><?=$pt['product_type']?></div>	
							<div style="padding:5px;">
<?
		$products = db_query("SELECT products.id, products.common_name, products.price, products.setup_fee, products.needs_quantity, product_types.product_type, request_note
								FROM products
								JOIN product_types ON products.producttype_id=product_types.id
								JOIN product_show ON product_show.product_id=products.id
								WHERE !products.deleted AND products.enabled AND !product_types.deleted AND product_types.enabled 
									AND product_show.show_id='".$_SESSION['show_id']."'
									AND products.id IN (SELECT product_id FROM session_order_items WHERE !deleted AND order_id='".$orderinfo['id']."' 
										AND timeslot_id='".$row['id']."' AND added_by_admin AND approved)
									AND products.producttype_id='".$pt['id']."'
								ORDER BY product_types.product_type, products.common_name
							","Get ".$pt['product_type']." Products");

							if(mysqli_num_rows($products) > 0) {
// 								if($pt['request_note'] != "") {
?> 					
								<div style="font-style:italic; padding-bottom:5px;">Session Reserved <?=$pt['product_type']?></div>
<?
//								} 
?>
								<table class="List" cellpadding="0" cellspacing="0" style="width:100%; margin-bottom:10px;">
									<tr>
										<th style="width:10px;">Qty</th>
										<th>Product</th>
									</tr>
<?
									$i = 1;
									while($p = mysqli_fetch_assoc($products)) {	
										$product_list .= $row['id']."_".$p['id'].",";	
?>							
										<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
											<td class="nocursor" style="white-space:nowrap;">
												<?=($p['needs_quantity']?$products_ordered[$row['id']][$p['id']]['qty']:'1')?>
											</td>
											<td class="nocursor">
<?
	$product_description = $p['common_name'];
	$product_price = "";
	$product_approved = "";
	//are we billing 3rd party?
	if($info['bill_other']) {
		//do we have a price or setup fee?
		if(isset($products_ordered[$row['id']][$p['id']])) {
			if($products_ordered[$row['id']][$p['id']]['price'] > 0 || $products_ordered[$row['id']][$p['id']]['setup'] > 0) {		
				$product_price .= " ($";
				if($products_ordered[$row['id']][$p['id']]['price'] > 0) {
					$product_price .= $products_ordered[$row['id']][$p['id']]['price'];
				}
				
				if($products_ordered[$row['id']][$p['id']]['setup'] > 0) {
					if(strlen($product_price) > 3) { $product_price .= " + "; }
					$product_price .= $products_ordered[$row['id']][$p['id']]['setup'];
					$product_price .= " setup";
				}
				$product_price .= " per item)";
			}
		} else {
			if($p['price'] > 0 || $p['setup_fee'] > 0) {
				$product_price .= " ($";
				if($p['price'] > 0) {
					$product_price .= $p['price'];
				}
				
				if($p['setup_fee'] > 0) {
					if(strlen($product_price) > 3) { $product_price .= " + "; }
					$product_price .= $p['setup_fee'];
					$product_price .= " setup";
				}
				$product_price .= " per item)";
			}
		}
	}
	
	if(isset($products_ordered[$row['id']][$p['id']]) && !$products_ordered[$row['id']][$p['id']]['aa']) {
		if($products_ordered[$row['id']][$p['id']]['approved']) {
			$product_approved .= ' <span style="color:red;">(Approved)</span>';
		} else {
			$product_approved .= ' <span style="color:red;">(Not Approved)</span>';
		}
	}
	
	if($info['bill_other']) {
		$p_qty = ($p['needs_quantity']?$products_ordered[$row['id']][$p['id']]['qty']:'1');
		$p_cost = (isset($products_ordered[$row['id']][$p['id']])?$products_ordered[$row['id']][$p['id']]['price']:$p['price']);
		$p_setup = (isset($products_ordered[$row['id']][$p['id']])?$products_ordered[$row['id']][$p['id']]['setup']:$p['setup_fee']);
		$p_approved = (isset($products_ordered[$row['id']][$p['id']])?$products_ordered[$row['id']][$p['id']]['approved']:'0');
	}
?>											
												<?=$product_description?><?=$product_price?><?=$product_approved?>
<?											if($info['bill_other']) { ?>
												<input type="hidden" id="product_<?=$row['id']?>_<?=$p['id']?>" value="on" />
												<input type="hidden" id="qty_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_qty?>" />
												<input type="hidden" id="price_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_cost?>" />
												<input type="hidden" id="setup_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_setup?>" />
												<input type="hidden" id="approved_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_approved?>" />
<?											} ?>												
											</td> 
										</tr>
<?
										$i++;
									}
?>
								</table>
<?								
							}
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
										AND timeslot_id='".$row['id']."' AND !added_by_admin))
									AND products.producttype_id='".$pt['id']."'
								ORDER BY product_types.product_type, products.common_name
							","Get ".$pt['product_type']." Products");

							if(mysqli_num_rows($products) > 0) {
 								if($pt['request_note'] != "") {
?> 					
								<div style="font-style:italic; padding-bottom:5px;"><?=$pt['request_note']?></div>
<?
								} 
?>
								<table class="List" cellpadding="0" cellspacing="0" style="width:100%; margin-bottom:10px;">
									<tr>
										<th style="width:10px;"></th>
										<th style="width:10px;">Qty</th>
										<th>Product</th>
									</tr>
<?
									$i = 1;
									while($p = mysqli_fetch_assoc($products)) {
										$product_list .= $row['id']."_".$p['id'].",";	
?>							
										<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
											<td class="nocursor">
<?
	$p_checked = "";
	$p_onchange = "";
	$p_orgvalue = "0";
	if(!$allow_ordering || (isset($products_ordered[$row['id']][$p['id']]) && $products_ordered[$row['id']][$p['id']]['aa'])) {
		$p_checked .= ' disabled="disabled"';
	}
	if(isset($products_ordered[$row['id']][$p['id']])) { $p_checked .= ' checked="checked"'; $p_orgvalue = "1"; }
	if($info['bill_other']) { $p_onchange .= "calculate_total_bill();"; }
?>											
												<input type="checkbox" name="product_ids_<?=$row['id']?>[]" id="product_<?=$row['id']?>_<?=$p['id']?>" 
												value="<?=$p['id']?>"<?=$p_checked?> onclick="check_product('<?=$row['id']?>_<?=$p['id']?>');<?=$p_onchange?>" /><input type="hidden" id="orig_product_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_orgvalue?>" />
											</td>
											<td class="nocursor" style="white-space:nowrap;">
<?
$quantity_options = "";
$p_orig_qty = '1'; 
if($p['needs_quantity']) {
	$quantity_options = ' type="text" size="3" maxlength="3"';
	if(isset($products_ordered[$row['id']][$p['id']])) { 
		$quantity_options .= ' value="'.$products_ordered[$row['id']][$p['id']]['qty'].'"';
		$p_orig_qty = $products_ordered[$row['id']][$p['id']]['qty'];
		if(!$allow_ordering) {
			$quantity_options .= ' disabled="disabled"';
		}
	} else {
		$quantity_options .= ' disabled="disabled"';
	}
	if($info['bill_other']) { $quantity_options .= ' onchange="calculate_total_bill();"'; }
	
} else {
	$quantity_options = ' type="hidden"';
	if(isset($products_ordered[$row['id']][$p['id']])) { $quantity_options .= ' value="1"'; $p_orig_qty = 1; }
}
?>
												<input name="qty_<?=$row['id']?>_<?=$p['id']?>" id="qty_<?=$row['id']?>_<?=$p['id']?>"<?=$quantity_options?> />
												<input type="hidden" id="orig_qty_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_orig_qty?>" />
											</td>
											<td class="nocursor">
<?
	$product_description = $p['common_name'];
	$product_price = "";
	$product_approved = "";
	$p_cost = '';
	$p_setup = '';
	//are we billing 3rd party?
	if($info['bill_other']) {
		//do we have a price or setup fee?
		if(isset($products_ordered[$row['id']][$p['id']])) {
			if($products_ordered[$row['id']][$p['id']]['price'] > 0 || $products_ordered[$row['id']][$p['id']]['setup'] > 0) {		
				$product_price .= " ($";
				if($products_ordered[$row['id']][$p['id']]['price'] > 0) {
					$product_price .= $products_ordered[$row['id']][$p['id']]['price'];
					$p_cost = $products_ordered[$row['id']][$p['id']]['price'];
				}
				
				if($products_ordered[$row['id']][$p['id']]['setup'] > 0) {
					if(strlen($product_price) > 3) { $product_price .= " + "; }
					$product_price .= $products_ordered[$row['id']][$p['id']]['setup'];
					$product_price .= " setup";
					$p_setup = $products_ordered[$row['id']][$p['id']]['setup'];
				}
				$product_price .= " per item)";
			}
		} else {
			if($p['price'] > 0 || $p['setup_fee'] > 0) {
				$product_price .= " ($";
				if($p['price'] > 0) {
					$product_price .= $p['price'];
					$p_cost = $p['price'];
				}
				
				if($p['setup_fee'] > 0) {
					if(strlen($product_price) > 3) { $product_price .= " + "; }
					$product_price .= $p['setup_fee'];
					$product_price .= " setup";
					$p_setup = $p['setup_fee'];
				}
				$product_price .= " per item)";
			}
		}
	}
	$p_approved=0;
	if(isset($products_ordered[$row['id']][$p['id']]) && !$products_ordered[$row['id']][$p['id']]['aa']) {
		if($products_ordered[$row['id']][$p['id']]['approved']) {
			$product_approved .= ' <span style="color:red;" id="app_'.$row['id'].'_'.$p['id'].'">(Approved)</span><span style="color:red;display:none;" id="notapp_'.$row['id'].'_'.$p['id'].'">(Not Approved)</span>';
			$p_approved = 1;
		} else if(!$products_ordered[$row['id']][$p['id']]['approved']) {
			$product_approved .= ' <span style="color:red;display:none;" id="app_'.$row['id'].'_'.$p['id'].'">(Approved)</span><span style="color:red;" id="notapp_'.$row['id'].'_'.$p['id'].'">(Not Approved)</span>';
			$p_approved = 0;
		} else {
			$product_approved .= ' <span style="color:red;display:none;" id="app_'.$row['id'].'_'.$p['id'].'">(Approved)</span><span style="color:red;display:none;" id="notapp_'.$row['id'].'_'.$p['id'].'">(Not Approved)</span>';
			$p_approved = 0;
		}
	}

?>											
												<label for="product_<?=$row['id']?>_<?=$p['id']?>"><?=$product_description?><?=$product_price?><?=$product_approved?></label>
												<input type="hidden" id="price_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_cost?>" />
												<input type="hidden" id="setup_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_setup?>" />
												<input type="hidden" id="approved_<?=$row['id']?>_<?=$p['id']?>" value="<?=$p_approved?>" />

											</td> 
										</tr>
<?
										$i++;
									}
?>
								</table>
<?								
							}
							if($pt['allowcustom']) {
?>
								<div class='FormName'>Custom Request</div>
<?
								if($pt['custom_top_instructions'] != "") {
?>
								<div><?=decode(nl2br($pt['custom_top_instructions']))?></div>
<?						
								}
?>
								<div class="date_time_box">
								<?=form_textarea(array('caption'=>'Custom Request','nocaption'=>'true','name'=>'custom_'.$row['id'].'_'.$pt['id'],'cols'=>'50','rows'=>'5','style'=>'width:100%;','value'=>(isset($custom_orders[$row['id']][$pt['id']])?$custom_orders[$row['id']][$pt['id']]['note']:''),'extra'=>(!$allow_ordering?'disabled="disabled"':'').' onchange="custom_check(\''.$row['id'].'_'.$pt['id'].'\');"'))?>
								<input type="hidden" id="orig_custom_<?=$row['id']?>_<?=$pt['id']?>" value="<?=(isset($custom_orders[$row['id']][$pt['id']])?$custom_orders[$row['id']][$pt['id']]['note']:'')?>" />
								</div>
<?
								if($pt['custom_instructions'] != "") {
?>
								<div><strong>NOTE:</strong> <?=decode(nl2br($pt['custom_instructions']))?></div>
<?						
								}
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
				<div style='font-weight: bold; margin-top:10px;'><?=$t++.". ".$question['question'].($question['required'] ? ' <span class="FormRequired">(Required)</span>' : '')?></div>
<?			
				if($question['fieldtype_id'] == 2 || $question['fieldtype_id'] == 3) {
?>
				<div style='padding-left:12px;padding-top:3px;'><input type='text' name='answer_<?=$row['id']?>_<?=$question['id']?>' id='answer_<?=$row['id']?>_<?=$question['id']?>' style='width: 400px;' value="<?=$question['answer']?>" /></div><?			
				} else if($question['fieldtype_id'] == 4) {
?>
				<div style='padding-left:12px;padding-top:3px;'><textarea name='answer_<?=$row['id']?>_<?=$question['id']?>' id='answer_<?=$row['id']?>_<?=$question['id']?>' cols='100' rows='5' style="width:400px;"><?=$question['answer']?></textarea></div><?			
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
				<label for='answer_<?=$row['id']?>_<?=$question['id']?>-<?=$j?>' style='padding-left: 10px; white-space:nowrap;'><input style="margin-top:3px;" type='checkbox' name='answer_<?=$row['id']?>_<?=$question['id']?>[]' id='answer_<?=$row['id']?>_<?=$question['id']?>-<?=$j?>' value='<?=$j?>' <?=(in_array($v,$tp_answer)?' checked="checked"':'')?>> <?=$v?></label><?
						$j++;
					}
				} else if($question['fieldtype_id'] == 7) {
					$tmp_options = explode('|||',$question['options']);
					$j = 0;
					foreach($tmp_options as $v) {
?>				<label for='answer_<?=$row['id']?>_<?=$question['id']?>-<?=$j?>' style='padding-left: 10px; white-space:nowrap;'><input style="margin-top:3px;" type='radio' name='answer_<?=$row['id']?>_<?=$question['id']?>[]' id='answer_<?=$row['id']?>_<?=$question['id']?>-<?=$j?>' value='<?=$j?>' <?=($v == $question['answer']?" checked='checked'":'')?>> <?=$v?></label><?
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
							<?=form_textarea(array('caption'=>'Bringing Own','nocaption'=>'true','name'=>'itemsbeingbrought'.$row['id'],'cols'=>'50','rows'=>'5','style'=>'width:100%;','value'=>(isset($order_notes[3][$row['id']]) ? $order_notes[3][$row['id']]['note']:'')))?>
<? // ,'extra'=>(!$allow_ordering?'disabled="disabled"':'') ?>
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
				<?=form_textarea(array('caption'=>'Order Notes','name'=>'ordernote','cols'=>'50','rows'=>'5','class'=>'full_width','value'=>(isset($order_notes[2]) ? $order_notes[2]['note']:''),'extra'=>(!$allow_ordering?'disabled="disabled"':'')))?>
				
<?
		if(date('Y-m-d') >= $show_data['show_signoff']) {
			if($orderinfo['sign_off_by'] == '') {
?>				
			<div style="margin-top:20px;">
			<?=form_text(array('caption'=>'Please initial the box below to indicate that you have reviewed this information, and you will make your own arrangements to provide any additional items that may not have been approved and/or you may require.','name'=>'sign_off_by','size'=>'10','maxlength'=>'5'))?>
			</div>
<?
			} else {
?>
			<div style="margin-top:20px; font-weight:bold;">
				Order has been signed off by '<?=strtoupper($orderinfo['sign_off_by'])?>' on <?=pretty_datetime($orderinfo['sign_off_date'])?>
			</div>
<?
			}
		}
?>
				
			</td>
		</tr>
	</table>
<?
	if($product_list != "") { $product_list = substr($product_list,0,-1); }
//	if($allow_ordering || (date('Y-m-d') >= $show_data['show_signoff'] && $orderinfo['sign_off_by'] == '')) {
?>
	<div class='FormButtons'>
		<?=form_button(array('type'=>'submit','name'=>'Save','value'=>($allow_ordering?'Save Request':'Save'),'extra'=>'onclick="document.getElementById(\'btnpress\').value=1;"'))?>
		<input type="hidden" name="query_string" value="<?=$_SERVER['QUERY_STRING']?>" />
		<input type="hidden" name="order_id" value="<?=($orderinfo['id']!=''?$orderinfo['id']:'')?>" />
		<input type="hidden" name="btnpress" id="btnpress" value="0" />
	</div>
<?
//	}
?>
	<input type="hidden" id="product_ids" value="<?=$product_list?>" />
</form>
<?
	}
?>