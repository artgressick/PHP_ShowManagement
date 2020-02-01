<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'opt_other'			=> 'checkboxes',
			'room_name' 		=> array('displayName' => 'Room Name','default' => 'asc'),
			'description' 		=> array('displayName' => 'Description'),
			'building_name'		=> array('displayName' => 'Building Name')
		);
	
		sortList('report',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'view.php?date='.$_REQUEST['date'].'&id=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);
?>
		<div style="margin-top:20px;"><?=form_button(array('type'=>'button','value'=>'Print Report','extra'=>'onclick="submit_form();"'))?></div>
	</form>
<?	
	}
?>