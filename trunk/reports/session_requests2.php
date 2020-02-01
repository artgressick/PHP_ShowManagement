<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name' 		=> array('displayName' => 'Class Name','default' => 'asc'),
			'start_date'		=> array('displayName' => 'Date','format'=>'date'),
			'start_time'		=> array('displayName' => 'Begin Time','format'=>'time'),
			'end_time'			=> array('displayName' => 'End Time','format'=>'time'),
			'sessiontype_name' 	=> array('displayName' => 'Session Type'),
			'room_number'		=> array('displayName' => 'Room Number'),
			'building_name'		=> array('displayName' => 'Building')
		);
		sortList('report',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}
?>