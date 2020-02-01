<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'building_name' 	=> array('displayName' => 'Building Name','default' => 'asc'),
			'access' 			=> array('displayName' => 'Access Date','format'=>'date'),
			'depart' 			=> array('displayName' => 'Departure Date','format'=>'date'),
			'opt_del' 			=> 'building_name'
		);
	
		sortList('buildings', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}