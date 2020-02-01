<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results,$info;
?>
		<table class="nav_bar" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
			<tr>
				<td class="nbmiddle" style=""><?=$info['product_type']?> products <?=form_button(array('type'=>'button','value'=>'Close Window','extra'=>'onclick="window.close();"'))?></td>
			</tr>
		</table>
<?
		$tableHeaders = array(
			'products' 			=> array('displayName' => 'Product'),
		);
	
		sortList('products',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}
?>