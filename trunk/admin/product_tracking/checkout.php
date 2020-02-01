<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;

		$tmp_products = db_query("SELECT products.id, CONCAT(products.product_name,' (',products.common_name,')') AS name, product_types.product_type AS optGroup FROM products JOIN product_types ON products.producttype_id=product_types.id WHERE !products.deleted AND products.track_product ORDER BY optGroup, name","Getting Products");
			$temp_products = array();
			while($row = mysqli_fetch_assoc($tmp_products)) {
				$temp_products[] = array('value'=>$row['id'],'display'=>$row['name'],'optGroup'=>$row['optGroup']);
			}
?>
	<div class='innerbody'>
		<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
			<tr>
				<td class="tcleft">
					<div class="colHeader">Room</div>
<?					
					$temprooms = db_query("SELECT rooms.id, room_name AS name, buildings.building_name AS optGroup FROM rooms JOIN buildings ON rooms.building_id=buildings.id WHERE !rooms.deleted ORDER BY optGroup, name","Getting Rooms");
					$rooms = array();
					while($row = mysqli_fetch_assoc($temprooms)) {
						$rooms[] = array('value'=>$row['id'],'display'=>$row['name'],'optGroup'=>$row['optGroup'],'extra'=>'onchange="checkfortracking();"');
					}
?>
					<?=form_select($rooms,array('type'=>'grouparray','caption'=>'-Select Room-','nocaption'=>'true','required'=>'true','name'=>'room_id','extra'=>'onchange="getroomassets();"'))?>
					<?=img(array('id'=>'spinner','style'=>'display:none;height:12px;','src'=>'spinner-1.gif'))?>
				</td>
				<td class="tcleft">
					<div class="colHeader">Product</div>
					<?=form_select($temp_products,array('type'=>'grouparray','nocaption'=>'true','caption'=>'-Select Product-','required'=>'true','name'=>'product_id','extra'=>'onchange="checkfortracking();"','style'=>'width:350px;'))?>
				</td>
				<td class="tcleft">
					<div class="colHeader">Tracking Number</div>
					<div><input type="text" name="tracking_number" id="tracking_number" size="40" maxlength="200" disabled="disabled" onchange="submittracking();" /></div>
				</td>
			</tr>
		</table>
		<div class='FormButtons' style="margin-top:20px;">
			<?=form_button(array('type'=>'submit','value'=>'Back to List','extra'=>'onclick="location.href=\'index.php\';"'))?>&nbsp;&nbsp;&nbsp;
			<a href="<?=$BF?>reports/room_session_products.php" target="_blank">Room/Session Products</a>
		</div>
	</div>

	<table cellpadding="0" cellspacing="0" class="page_info" border="0" id="room_assets" style="margin-top:20px; display:none;">
		<tr>
			<td class="headleft" width="4">&nbsp;</td>
			<td class="headmiddle" style="padding-left:5px;">Room Assets</td>
			<td class="headright">&nbsp;</td>
		</tr>
		<tr>
			<td class="body" colspan="5" id="asset_data"></td>
		</tr>
	</table>
<?
	}
?>