<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'sessioncat_name' 	=> array('displayName' => 'Session Category Name','default' => 'asc'),
			'opt_del' 			=> 'sessioncat_name'
		);
	
		sortList('sessioncats', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}