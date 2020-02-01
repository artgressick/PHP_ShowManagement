<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'room_number' 		=> array('displayName' => 'Room Number','default' => 'asc'),
			'description' 		=> array('displayName' => 'Description'),
			'building_name' 	=> array('displayName' => 'Building Name')
		);
	
		sortList('rooms', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'viewroom.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}