<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'show_name' 		=> array('displayName' => 'Show Name'),
			'start_date' 		=> array('displayName' => 'Start Date','default' => 'asc','format'=>'date'),
			'end_date' 			=> array('displayName' => 'End Date','format'=>'date'),
			'status_name' 		=> array('displayName' => 'Status'),
			'opt_del' 			=> 'show_name'
		);
	
		sortList('shows', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}