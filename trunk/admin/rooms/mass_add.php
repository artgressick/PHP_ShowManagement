<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;
?>
	<form action="" method="post" id="idForm" onsubmit="">
	<div class='innerbody'>
		<table cellpadding="5" cellspacing="0" style="width:100%;">
			<tr>
				<td style="font-weight:bold; font-size:15px; text-align:left; vertical-align:top; width:50%;">Products</td>
				<td style="font-weight:bold; font-size:15px; text-align:left; vertical-align:top; width:50%;">Rooms</td>
			</tr>
			<tr>
				<td style="text-align:left; vertical-align:top; width:50%;">
<?
				$products = db_query("
					SELECT products.id, products.product_name, products.common_name, product_types.product_type, products.needs_quantity 
					FROM products 
					JOIN product_types ON products.producttype_id=product_types.id 
					JOIN product_show ON products.id=product_show.product_id 
					WHERE !products.deleted AND products.enabled AND product_types.enabled AND !product_types.deleted 
						AND product_show.show_id='".$_SESSION['show_id']."'
					ORDER BY common_name, product_name, product_type","Getting Products");
?>					
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
						<thead>
							<tr>
								<th style="width:10px;"></th>
								<th style="width:10px;">Qty</th>
								<th>Product</th>
							</tr>
						</thead>
						<tbody>
<?
						$i = 1;
						while($row = mysqli_fetch_assoc($products)) {		
?>							
							<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
								<td class="nocursor">
									<input type="checkbox" name="products[]" id="product<?=$row['id']?>" onchange="check_product('<?=$row['id']?>');" value="<?=$row['id']?>" />
								</td>
								<td class="nocursor" style="white-space:nowrap;">
<?
							if($row['needs_quantity']) {
?>
									<input type="text" name="qty<?=$row['id']?>" id="qty<?=$row['id']?>" size="4" maxlength="3" disabled="disabled" onchange="checkvalue('<?=$row['id']?>');" />
<?							
							} else {
?>
									<input type="hidden" name="qty<?=$row['id']?>" id="qty<?=$row['id']?>" />
<?							
							}
?>									
								</td>
								<td class="nocursor">
									<label for="product<?=$row['id']?>"><?=$row['common_name']?> (<?=$row['product_name']?>)</label>
								</td>
							</tr>
<?
							$i++;
						}
?>			
						</tbody>
					</table>
				</td>
				<td style="text-align:left; vertical-align:top; width:50%;">
<?
			$q = "SELECT rooms.*, buildings.building_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !rooms.deleted AND rooms.show_id=".$_SESSION['show_id']."
					ORDER BY rooms.room_name";
				
			$results = db_query($q,"getting rooms");
?>				
					<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
						<thead>
							<tr>
								<th style="width:10px;"></th>
								<th>Room</th>
								<th>Building</th>
							</tr>
						</thead>
						<tbody>
<?
						$i = 1;
						while($row = mysqli_fetch_assoc($results)) {		
?>							
							<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
								<td class="nocursor">
									<input type="checkbox" name="rooms[]" id="room<?=$row['id']?>" value="<?=$row['id']?>" />
								</td>
								<td class="nocursor">
									<label for="room<?=$row['id']?>"><?=$row['room_name']?> <?=($row['description']!=''?'('.$row['description'].')':'')?></label>
								</td>
								<td class="nocursor" style="white-space:nowrap;">
									<label for="room<?=$row['id']?>"><?=$row['building_name']?></label>
								</td>
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
		<div class='FormButtons'>
			<?=form_button(array('type'=>'submit','value'=>'Add Product(s) to Room(s)'))?>
		</div>
	</div>
	</form>
<?
	}
?>