<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name'		=> array('displayName' => 'Session Name'),
			'sessiontype_name'	=> array('displayName' => 'Session Type'),
			'room_name' 		=> array('displayName' => 'Room Name'),
			'building_name'		=> array('displayName' => 'Building Name'),
			'prep_time' 		=> array('displayName' => 'Prep Time','default' => 'asc','format'=>'time'),
			'start_time' 		=> array('displayName' => 'Start Time','format'=>'time'),
			'checked_datetime'	=> array('displayName' => 'Checked Date Time','format'=>'date_time'),
			'full_name'			=> array('displayName' => 'Checked In By'),
		);
		
		if($_SESSION['group_id'] == 1) {
			$tableHeaders['opt_other'] = 'reset_session';
		}
	
		sortList('sessioncheckin',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);
?>
	<table align="center" width='300' cellpadding="5" cellspacing="0" style='margin-top:20px;'>
		<tr>
			<td width='33%' style='background:#92ff92; text-align:center; vertical-align:top; font-size:8px;'>Checked In</td>
			<td width='34%' style='background:#f9ff5f; text-align:center; vertical-align:top; font-size:8px;'>15 - 5 Minute Warning</td>
			<td width='33%' style='background:#ff9696; text-align:center; vertical-align:top; font-size:8px;'>5 Minute Warning or Started</td>
		</tr>
	</table>


	<form action="" method="post" id="idForm" style='padding:0;margin:0;'>
		<input type='hidden' name='idReset' id='idReset' value='' />
		<input type='hidden' name='scheckoff' id='scheckoff' value='' />
	</form>
<?	
	}