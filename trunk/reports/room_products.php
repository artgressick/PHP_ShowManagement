<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'room_name' 		=> array('displayName' => 'Room Name','default' => 'asc'),
			'description' 		=> array('displayName' => 'Description'),
			'building_name'		=> array('displayName' => 'Building Name'),
			'product_name'		=> array('displayName' => 'Product'),
			'common_name'		=> array('displayName' => 'Common Name'),
			'product_type'		=> array('displayName' => 'Product Type'),
			'quantity'		=> array('displayName' => 'Quantity'),
		);
		if($_SESSION['group_id'] == 1 && $_REQUEST['show_vendor'] == 1) {
			$tableHeaders['vendor_name'] = array('displayName' => 'Vendor');
		}
		sortList('rooms',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}