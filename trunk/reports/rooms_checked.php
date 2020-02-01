<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'room_name' 		=> array('displayName' => 'Room Name','default' => 'asc'),
			'description' 		=> array('displayName' => 'Description'),
			'building_name'		=> array('displayName' => 'Building Name'),
			'checked_datetime'	=> array('displayName' => 'Checked Date Time','format'=>'date_time'),
			'full_name'			=> array('displayName' => 'Checked In By'),
		);
		if($_SESSION['group_id'] == 1) {
			$tableHeaders['opt_other'] = 'reset_room';
		}

	
		sortList('roomschecked',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);
?>
	<form action="" method="post" id="idForm" style='padding:0;margin:0;'>
		<input type='hidden' name='idReset' id='idReset' value='' />
		<input type='hidden' name='rcheckoff' id='rcheckoff' value='' />
	</form>
<?	
	}
?>