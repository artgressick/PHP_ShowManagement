<?php
	include('_controller.php');

	function sitm() {
		global $BF;


		$temprooms = db_query("SELECT rooms.id, room_name AS name, buildings.building_name AS optGroup FROM rooms JOIN buildings ON rooms.building_id=buildings.id WHERE !rooms.deleted ORDER BY optGroup, name","Getting Rooms");
		$rooms = array();
		while($row = mysqli_fetch_assoc($temprooms)) {
			$rooms[] = array('value'=>$row['id'],'display'=>$row['name'],'optGroup'=>$row['optGroup'],'extra'=>'onchange="checkfortracking();"');
		}
					
		$tmp_products = db_query("SELECT products.id, CONCAT(products.product_name,' (',products.common_name,')') AS name, product_types.product_type AS optGroup FROM products JOIN product_types ON products.producttype_id=product_types.id WHERE !products.deleted AND products.track_product ORDER BY optGroup, name","Getting Products");
		$temp_products = array();
		while($row = mysqli_fetch_assoc($tmp_products)) {
			$temp_products[] = array('value'=>$row['id'],'display'=>$row['name'],'optGroup'=>$row['optGroup']);
		}

?>
		<div style="text-align:center;">
	
			<div class="colHeader" style="margin-top:10px;">Room</div>
			<?=form_select($rooms,array('type'=>'grouparray','caption'=>'-Select Room-','nocaption'=>'true','required'=>'true','name'=>'room_id','style'=>'width:300px;'))?>

			<div class="colHeader" style="margin-top:20px;">Product</div>
			<?=form_select($temp_products,array('type'=>'grouparray','nocaption'=>'true','caption'=>'-Select Product-','required'=>'true','name'=>'product_id','extra'=>'onchange="checkfortracking();"','style'=>'width:300px;'))?>

			<div class="colHeader" style="margin-top:20px;">Tracking Number</div>
			<div><input type="text" name="tracking_number" id="tracking_number" size="40" maxlength="200" disabled="disabled" onchange="submittracking();" value="Select Room and Product" /></div>

			<div style="margin-top:20px;"><a class="blue button" href="#">Submit</a></div>

		</div>
<?
	}
?>