<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$info,$results;
?>
	<table cellpadding="0" cellspacing="0" class="tabs">
		<tr>
			<th class="tab" onclick="location.href='edit.php?key=<?=$info['lkey']?>';">Information</th><th class="space"><!-- BLANK --></th>
			<th class="current" onclick="location.href='products.php?key=<?=$info['lkey']?>';">Room Products</th><th class="space"><!-- BLANK --></th>
		</tr>
	</table>
	<div class='innerbody'>
<?
		$tableHeaders = array(
			'product_name' 		=> array('displayName' => 'Product Name','default' => 'asc'),
			'product_type' 		=> array('displayName' => 'Product Type'),
			'opt_other' 		=> 'quantity',
			'opt_del' 			=> 'product_name'
		);
	
		sortList('room_products', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);
?>
		<div class='FormButtons'>
			<?=form_button(array('type'=>'button','value'=>'Save','extra'=>'onclick="location.href=\'products.php?key='.$info['lkey'].'\';"'))?>
		</div>
	</div>
<?

	}
?>