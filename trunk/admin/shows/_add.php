<?
	include_once($BF.'components/add_functions.php');
	$table = 'shows'; # added so not to forget to change the insert AND audit
	$salt = makekey();
	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		show_name = '". encode($_POST['show_name']) ."',
		start_date = '". date('Y-m-d', strtotime($_POST['start_date'])) ."',
		end_date = '". date('Y-m-d', strtotime($_POST['end_date'])) ."',
		lock_requests = '". date('Y-m-d', strtotime($_POST['lock_requests'])) ."',
		show_room_data = '". date('Y-m-d', strtotime($_POST['show_room_data'])) ."',
		show_signoff = '". date('Y-m-d', strtotime($_POST['show_signoff'])) ."',
		status_id = '". $_POST['status_id'] ."',
		session_bill = '". $_POST['session_bill'] ."',
		landing_page = '". encode($_POST['landing_page']) ."',
		bill_name = '". encode($_POST['bill_name']) ."',
		bill_address1 = '". encode($_POST['bill_address1']) ."',
		bill_address2 = '". encode($_POST['bill_address2']) ."',
		bill_address3 = '". encode($_POST['bill_address3']) ."',
		bill_local = '". encode($_POST['bill_local']) ."',
		bill_state = '". encode($_POST['bill_state']) ."',
		bill_postal = '". encode($_POST['bill_postal']) ."',
		bill_country = '". encode($_POST['bill_country']) ."',
		bill_phone = '". encode($_POST['bill_phone']) ."',
		bill_fax = '". encode($_POST['bill_fax']) ."',
		bill_email = '". encode($_POST['bill_email']) ."',
		created_at = NOW(), updated_at = NOW()
	";
	
	# if there database insertion is successful	
	if(db_query($q,"Insert into ". $table)) {

		// This is the code for inserting the Audit Page
		// Type 1 means ADD NEW RECORD, change the TABLE NAME also
		global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
		$newID = mysqli_insert_id($mysqli_connection);
	
		$q = "INSERT INTO audit SET 
			audittype_id=1, 
			record_id='". $newID ."',
			new_value='". encode($_POST['show_name']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Show: " . encode($_POST['show_name']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Show.');
	}
?>