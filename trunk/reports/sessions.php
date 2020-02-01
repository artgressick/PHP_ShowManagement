<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name'		=> array('displayName' => 'Session Name'),
			'sessiontype_name'	=> array('displayName' => 'Session Type'),
			'room_name' 		=> array('displayName' => 'Room Name'),
			'building_name'		=> array('displayName' => 'Building Name'),
			'prep_time' 		=> array('displayName' => 'Prep Time','default' => 'asc','format'=>'time'),
			'start_time' 		=> array('displayName' => 'Start Time','format'=>'time'),
			'end_time' 			=> array('displayName' => 'End Time','format'=>'time'),
			'strike_time' 		=> array('displayName' => 'Strike Time','format'=>'time'),
		);
		
	
		sortList('reports',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);
	}