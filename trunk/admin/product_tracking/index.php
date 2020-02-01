<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'product_name' 		=> array('displayName' => 'Product'),
			'product_type' 		=> array('displayName' => 'Type'),
			'room_name'			=> array('displayName' => 'Room'),
			'building_name' 	=> array('displayName' => 'Building'),
			'user_name' 		=> array('displayName' => 'Checked Out By'),
			'check_out'			=> array('displayName' => 'Checked Out','default' => 'asc'),
			'tracking_number'	=> array('displayName' => 'Tracking Number'),
		);
	
		sortList('product_tracking', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}
?>