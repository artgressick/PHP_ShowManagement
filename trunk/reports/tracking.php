<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'tracking_number' 	=> array('displayName' => 'Tracking Number'),
			'product_name' 		=> array('displayName' => 'Product'),
			'product_type'		=> array('displayName' => 'Type'),
			'check_out'			=> array('displayName' => 'Checked Out On','format'=>'date_time2','default' => 'asc'),
			'user_name'			=> array('displayName' => 'Checked Out By'),
			'room_name'			=> array('displayName' => 'Room'),
			'building_name'		=> array('displayName' => 'Building'),
			'check_in'			=> array('displayName' => 'Checked In On','format'=>'date_time2'),
			'user_name2'		=> array('displayName' => 'Checked In By'),
		);
		sortList('tracking',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}