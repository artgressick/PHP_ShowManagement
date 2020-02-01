<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<div class="colHeader2">Room Information</div>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
					<?=form_text(array('caption'=>'Room Name','display'=>'true','value'=>$info['room_name']))?>
					<?=form_text(array('caption'=>'Room Description','display'=>'true','value'=>$info['description']))?>
					<?=form_text(array('caption'=>'Building Name','display'=>'true','value'=>$info['building_name']))?>
				</td>
				<td class="tcgutter"></td>
				<td class="tcright">
					<?=form_text(array('caption'=>'Date','display'=>'true','value'=>date('n/j/Y',strtotime($_REQUEST['date']))))?>
					<?=form_text(array('caption'=>'Has Internet Access','display'=>'true','value'=>(!$info['internet_access']?'NO':'Yes')))?>
				</td>
			</tr>
		</table>
		<div class="colHeader2">Room Products</div>
<?
		//First Product Types
		$q = "SELECT pt.id, pt.product_type
				FROM room_products AS rp 
				JOIN products AS p ON rp.product_id=p.id
				JOIN product_types AS pt ON p.producttype_id=pt.id
				WHERE !p.deleted AND !pt.deleted AND pt.id != 2 AND rp.room_id = '".$info['id']."'
				GROUP BY pt.id
				ORDER BY product_type";
				
		$rpt = db_query($q,'Get Room Product Types');
		
		if(mysqli_num_rows($rpt) > 0) {
		
			$halfpt = ceil(mysqli_num_rows($rpt) / 2);
			$cnt = 1; //Indicates Column 1
			$col2 = false;
?>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
<?
			
			while($pt_row = mysqli_fetch_assoc($rpt)) {
				if($cnt > $halfpt && $col2 == false) { // go into second column
?>
				</td>
				<td class="tcgutter"></td>
				<td class="tcright">	
<?				
					$col2 = true;
				}
?>
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
						<tr>
							<th><?=$pt_row['product_type']?> Product</th>
							<th style="width:50px;">Quantity</th>
						</tr>
						
<?
				$q = "SELECT rp.id, rp.quantity, p.product_name, p.track_product, p.common_name
						FROM room_products AS rp 
						JOIN products AS p ON rp.product_id=p.id
						WHERE !p.deleted AND p.producttype_id = '".$pt_row['id']."' AND rp.room_id = '".$info['id']."'
						ORDER BY product_name";
						
				$rp = db_query($q,'Getting Products');
				$rct = 0;
				while($rp_row = mysqli_fetch_assoc($rp)) {
?>
						<tr class="<?=($rct++%2 ? 'ListOdd' : 'ListEven')?>">
							<td><?=$rp_row['product_name']?> (<?=$rp_row['common_name']?>)<?=($rp_row['track_product']?'*':'')?></td>
							<td style="text-align:center;"><?=$rp_row['quantity']?></td>
						</tr>
<?					
				}
?>
						</tr>
					</table>
<?
				$cnt++;				
			}
?>		
				</td>
			</tr>
		</table>
<?
		} else {
?>
		<div style="padding:10px; text-align:center;">No Products Assigned to this Room</div>
<?		
		}
?>
		<div class="colHeader2">Room Tracked Products</div>
<?
		$assets = db_query("SELECT products.id, COUNT(product_tracking.id) AS product_count, CONCAT(products.product_name,' (',products.common_name,')') AS product_name, product_types.product_type
					FROM product_tracking
					JOIN products ON product_tracking.product_id=products.id 
					JOIN product_types ON products.producttype_id=product_types.id
					WHERE product_tracking.show_id='".$_SESSION['show_id']."' AND product_tracking.room_id='".$info['id']."' 
						AND product_tracking.check_in IS NULL
					GROUP BY product_tracking.product_id
					ORDER BY product_name
		","Get asset counts grouped by product");
				
		if(mysqli_num_rows($assets) > 0) {
			$halfpt = ceil(mysqli_num_rows($assets) / 2);
			
			$cnt = 1; //Indicates Column 1
			$col2 = false;
?>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
						<tr>
							<th>Product</th>
							<th style="width:50px;">Quantity</th>
						</tr>

<?			
			$rct = 0;
			while($asset_row = mysqli_fetch_assoc($assets)) {

				if($cnt > $halfpt && $col2 == false) {
?>
					</table>
				</td>
				<td class="tcgutter"></td>
				<td class="tcright">
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
						<tr>
							<th>Product</th>
							<th style="width:50px;">Quantity</th>
						</tr>
<?				
					$col2 = true;
				}
?>
						<tr class="<?=($rct++%2 ? 'ListOdd' : 'ListEven')?>">
							<td><?=$asset_row['product_name']?> - <?=$asset_row['product_type']?>)<?=($rp_row['track_product']?'*':'')?></td>
							<td style="text-align:center;"><?=$asset_row['product_count']?></td>
						</tr>
<?					
				$cnt++;
			}
?>		
					</table>
				</td>
			</tr>
		</table>
<?
		} else {
?>
		<div style="padding:10px; text-align:center;">No Products Checked into this Room</div>
<?		
		}
?>
		<div class="colHeader2">Sessions</div>
<?
		$q = "SELECT ts.*, c.class_name, c.speaker, st.sessiontype_name
				FROM time_slots AS ts
				JOIN classes AS c ON ts.class_id=c.id
				JOIN sessiontypes AS st ON c.sessiontype_id=st.id
				WHERE !ts.deleted AND !c.deleted AND !st.deleted AND ts.start_date='".$_REQUEST['date']."' AND c.show_id = '".$_SESSION['show_id']."' AND ts.room_id='".$info['id']."'
				ORDER BY ts.start_time, ts.end_time, ts.prep_time
		";
		$sessions = db_query($q,'Getting Sessions for Room');
		$count = 1;
		
		while($s_row = mysqli_fetch_assoc($sessions)) {
			if($count++ > 1) {
?>
			<div class="colHeader2"> </div>
<?
			}
?>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
					<?=form_text(array('caption'=>'Session Name','display'=>'true','value'=>$s_row['class_name']))?>
					<?=form_text(array('caption'=>'Session Type','display'=>'true','value'=>$s_row['sessiontype_name']))?>
					<?=form_text(array('caption'=>'Speaker','display'=>'true','value'=>$s_row['speaker']))?>
					<?=form_text(array('caption'=>'Date','display'=>'true','value'=>date('n/j/Y',strtotime($s_row['start_date']))))?>
				</td>
				<td class="tcgutter"></td>
				<td class="tcright">
					<?=form_text(array('caption'=>'Prep Time','display'=>'true','value'=>date('g:i a',strtotime($s_row['prep_time']))))?>
					<?=form_text(array('caption'=>'Start Time','display'=>'true','value'=>date('g:i a',strtotime($s_row['start_time']))))?>
					<?=form_text(array('caption'=>'End Time','display'=>'true','value'=>date('g:i a',strtotime($s_row['end_time']))))?>
					<?=form_text(array('caption'=>'Strike Time','display'=>'true','value'=>date('g:i a',strtotime($s_row['strike_time']))))?>
				</td>
			</tr>
		</table>
<?
			$spt = db_query("SELECT pt.id, pt.product_type
								FROM session_order_items AS soi
								JOIN products AS p ON soi.product_id=p.id
								JOIN product_types AS pt ON p.producttype_id=pt.id
								JOIN time_slots AS ts ON soi.timeslot_id=ts.id
								JOIN classes AS c ON ts.class_id=c.id
								JOIN session_orders AS so ON so.session_id=c.id
								WHERE !p.deleted AND !pt.deleted AND so.status_id=2 AND soi.approved AND ts.id = '".$s_row['id']."' 
									AND ts.start_date='".$_REQUEST['date']."' AND c.show_id = '".$_SESSION['show_id']."' 
									AND ts.room_id='".$info['id']."'
								GROUP BY pt.id
								ORDER BY pt.product_type
			",'Getting Session Product Types');
			if(mysqli_num_rows($spt) > 0) {
			
				$halfpt = ceil(mysqli_num_rows($spt) / 2);
				
				$cnt = 1; //Indicates Column 1
				$col2 = false;
?>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
<?

				while($st_row = mysqli_fetch_assoc($spt)) {
					if($cnt > $halfpt) {
?>
				</td>
				<td class="tcgutter"></td>
				<td class="tcright">	
<?				
						$col2 = true;
					}
	
?>
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
						<tr>
							<th><?=$st_row['product_type']?> Product</th>
							<th style="width:50px;">Quantity</th>
						</tr>
						
<?
					$q = "SELECT soi.id, soi.quantity, p.product_name, p.track_product, p.common_name
							FROM session_order_items AS soi
							JOIN products AS p ON soi.product_id=p.id
							JOIN time_slots AS ts ON soi.timeslot_id=ts.id
							JOIN classes AS c ON ts.class_id=c.id
							JOIN session_orders AS so ON so.session_id=c.id
							WHERE !p.deleted AND p.producttype_id = '".$st_row['id']."' AND ts.id = '".$s_row['id']."' AND so.status_id=2 
								AND soi.approved AND ts.start_date='".$_REQUEST['date']."' AND c.show_id = '".$_SESSION['show_id']."' 
								AND ts.room_id='".$info['id']."'
							ORDER BY product_name
							";
							
					$sp = db_query($q,'Getting Products');
					$rct = 0;
					while($sp_row = mysqli_fetch_assoc($sp)) {
?>
						<tr class="<?=($rct++%2 ? 'ListOdd' : 'ListEven')?>">
							<td><?=$sp_row['product_name']?> (<?=$sp_row['common_name']?>)<?=($sp_row['track_product']?'*':'')?></td>
							<td style="text-align:center;"><?=$sp_row['quantity']?></td>
						</tr>
<?					
					}		
?>
						</tr>
					</table>
<?
					$cnt++;
				}
?>		
				</td>
			</tr>
		</table>
<?
			
			} else {
?>
		<div style="padding:10px; text-align:center;">No Products Assigned to this Room</div>
<?		
			}
		}

?>
	</div>
<?
	}
?>