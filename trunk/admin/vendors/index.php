<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'vendor_name' 		=> array('displayName' => 'Vendor Name','default' => 'asc'),
			'opt_del' 			=> 'vendor_name'
		);
	
		sortList('vendors', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}