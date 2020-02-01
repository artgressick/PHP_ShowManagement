<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'product_type' 		=> array('displayName' => 'Product Type Name','default' => 'asc'),
			'opt_del' 			=> 'product_type'
		);
	
		sortList('producttypes', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}