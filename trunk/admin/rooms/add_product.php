<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form enctype="multipart/form-data" action="" method="post" id="idForm" onsubmit="return error_check()">

			<div class="colHeader2">Add Product to Room</div>
<?					
			$products = db_query("
								SELECT products.id, products.product_name as name, product_types.product_type AS optGroup 
								FROM products 
								JOIN product_types ON products.producttype_id=product_types.id 
								JOIN product_show ON products.id=product_show.product_id 
								WHERE !products.deleted AND products.enabled AND product_types.enabled AND !product_types.deleted AND product_show.show_id=".$_SESSION['show_id']." AND 
								products.id NOT IN (SELECT room_products.product_id FROM room_products WHERE !room_products.deleted AND room_products.room_id=".$info['id'].")
								ORDER BY optGroup, name","Getting Products");
?>
			<?=form_select($products,array('caption'=>'Product','required'=>'true','name'=>'product_id','extra'=>'onchange="is_qty_needed(\''.$BF.'\',this.value)"'))?><?=img(array('id'=>'spinner','style'=>'display:none;','src'=>'spinner-1.gif'))?>
			<div id='quantity_question' style="display:none;">
			<?=form_text(array('caption'=>'Quantity','name'=>'quantity','size'=>'10','maxlength'=>'10','value'=>'1'))?>
			</div>
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Add Another','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'add_product.php?key='.$info['lkey'].'\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'submit','value'=>'Add and Continue','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'products.php?key='.$info['lkey'].'\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'button','value'=>'Cancel','extra'=>'onclick="location.href=\'products.php?key='.$info['lkey'].'\';"'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'moveTo'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$info['lkey']))?>
			</div>
		</form>
	</div>
<?
	}
?>