<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

			$tmp_products = db_query("SELECT products.id, CONCAT(products.product_name,' (',products.common_name,')') AS name, product_types.product_type AS optGroup FROM products JOIN product_types ON products.producttype_id=product_types.id WHERE !products.deleted AND products.track_product ORDER BY optGroup, name","Getting Products");
			$temp_products = array();
			while($row = mysqli_fetch_assoc($tmp_products)) {
				$temp_products[] = array('value'=>$row['id'],'display'=>$row['name'],'optGroup'=>$row['optGroup']);
			}


?>
	<form action="" method="post" id="idForm" onsubmit="return error_check()">
		<table cellpadding="0" cellspacing="0" style="width:100%;" class="List">
			<thead>
				<tr>
					<th>Tracking Number</th>
					<th>Product</th>
				</tr>
			</thead>
			<tbody>
<?
			$i= 0;
			while($row = mysqli_fetch_assoc($results)) {		
?>							
				<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
					<td class="nocursor"><span id='tn_<?=$row['tracking_number']?>'><?=$row['tracking_number']?></span>
						<input type="text" name="ntn_<?=$row['tracking_number']?>" id="ntn_<?=$row['tracking_number']?>" size="30" maxlength="200" value="<?=$row['tracking_number']?>" style="display:none;" />&nbsp;&nbsp;&nbsp;
						<input type="button" onclick="edit_number('<?=$row['tracking_number']?>')" id="ed_<?=$row['tracking_number']?>" value="Edit" />
						<input type="button" onclick="save_number('<?=$row['tracking_number']?>')" id="sv_<?=$row['tracking_number']?>" value="Save" style="display:none;" />
						<input type="hidden" name="otn_<?=$row['tracking_number']?>" value="<?=$row['tracking_number']?>" />
						</td>
					<td class="nocursor">
						<?=form_select($temp_products,array('type'=>'grouparray','nocaption'=>'true','caption'=>'-Select Product-','required'=>'true','name'=>'product_id_'.$row['tracking_number'],'value'=>$row['product_id']))?>
					</td>
				</tr>
<?
				$i++;
			}
?>			
			</tbody>
		</table>
		<div class='FormButtons'>
			<?=form_button(array('type'=>'submit','name'=>'rtracking','value'=>'Save'))?>
		</div>
	</form>
<?
	}
?>