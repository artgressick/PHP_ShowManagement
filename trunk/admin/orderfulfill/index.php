<?php
	include('_controller.php');

	function sitm() { 
		global $BF,$results;

		$tableHeaders = array(
			'class_name' 		=> array('displayName' => 'Session Name'),
			'sessiontype_name'	=> array('displayName' => 'Session Type','rowstyle'=>'white-space:nowrap;'),
			'billto'			=> array('displayName' => 'Bill To','rowstyle'=>'white-space:nowrap;'),
			'start_dt'			=> array('displayName' => 'Starting Date/Time','default' => 'asc','format'=>'date_time','rowstyle'=>'white-space:nowrap;'),
			'time_count'		=> array('displayName' => '# Sessions','rowstyle'=>'white-space:nowrap; text-align:center;'),
			'quote_name'		=> array('displayName' => 'Order Number','rowstyle'=>'white-space:nowrap;'),
			'order_status'		=> array('displayName' => 'Order Status','rowstyle'=>'white-space:nowrap;')
		);
	
		sortList('orders',		# Table Name
			$tableHeaders,		# Table Name
			$results,			# Query results
			'order.php?key=',	# The linkto page when you click on the row
			'width: 100%;', 	# Additional header CSS here
			''
		);

	}