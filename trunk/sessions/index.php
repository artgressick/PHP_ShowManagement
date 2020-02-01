<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name' 		=> array('displayName' => 'Session Name'),
			'sessioncat_name'	=> array('displayName' => 'Session Category','rowstyle'=>'white-space:nowrap;'),
			'sessiontype_name'	=> array('displayName' => 'Session Type','rowstyle'=>'white-space:nowrap;'),
			'billto'			=> array('displayName' => 'Bill To','rowstyle'=>'white-space:nowrap;'),
			'start_dt'			=> array('displayName' => 'Starting Date/Time','default' => 'asc','format'=>'date_time','rowstyle'=>'white-space:nowrap;'),
			'time_count'		=> array('displayName' => '# Sessions','rowstyle'=>'white-space:nowrap; text-align:center;'),
			'room_name'			=> array('displayName' => 'Room Name'),
			'speaker'			=> array('displayName' => 'Speaker'),
		);
	
		sortList('classes',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'view.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}