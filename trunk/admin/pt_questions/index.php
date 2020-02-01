<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'association' 		=> array('displayName' => 'Session Type to Product Type Association','default' => 'asc')
		);
	
		sortList('sessiontype_producttypes', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?id=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}