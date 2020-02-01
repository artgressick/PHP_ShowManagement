<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'sessiontype_name' 		=> array('displayName' => 'Session Type Name','default' => 'asc'),
			'opt_del' 				=> 'sessiontype_name'
		);
	
		sortList('sessiontypes', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}