<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name' 		=> array('displayName' => 'Session Name'),
			'sessiontype_name'	=> array('displayName' => 'Session Type','rowstyle'=>'white-space:nowrap;'),
			'speaker'			=> array('displayName' => 'Speaker'),
			'billto'			=> array('displayName' => 'Bill To','rowstyle'=>'white-space:nowrap;'),
			'start_dt'			=> array('displayName' => 'Starting Date/Time','default' => 'asc','format'=>'date_time','rowstyle'=>'white-space:nowrap;'),
			'time_count'		=> array('displayName' => '# Sessions','rowstyle'=>'white-space:nowrap; text-align:center;')
		);
	
		sortList('report',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}