<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'room_name' 		=> array('displayName' => 'Room Name','default' => 'asc'),
			'description' 		=> array('displayName' => 'Description'),
			'building_name'		=> array('displayName' => 'Building Name'),
		);
	
		sortList('rooms',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}