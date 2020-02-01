<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'product_name' 		=> array('displayName' => 'Product Name','default' => 'asc'),
			'common_name' 		=> array('displayName' => 'Common Name'),
			'product_type'		=> array('displayName' => 'Product Type'),
			'opt_del' 			=> 'product_name'
		);
	
		sortList('products', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}