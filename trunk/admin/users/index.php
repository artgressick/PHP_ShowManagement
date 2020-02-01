<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'first_name' 		=> array('displayName' => 'First Name'),
			'last_name' 		=> array('displayName' => 'Last Name','default' => 'asc'),
			'email' 			=> array('displayName' => 'Email'),
			'group_name' 		=> array('displayName' => 'Group'),
			'opt_del' 			=> 'class_name'
		);
	
		sortList('users', 		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'edit.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}