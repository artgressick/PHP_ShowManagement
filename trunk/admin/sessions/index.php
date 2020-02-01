<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name' 		=> array('displayName' => 'Session Name'),
			'sessiontype_name'	=> array('displayName' => 'Session Type'),
			'start_dt'			=> array('displayName' => 'Starting Date/Time','default' => 'asc','format'=>'date_time'),
			'room_name'			=> array('displayName' => 'Room Name'),
			'opt_del' 			=> 'class_name'
			
		);
	
		sortList('classes',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}