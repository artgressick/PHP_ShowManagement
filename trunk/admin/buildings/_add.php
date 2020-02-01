<?
	include_once($BF.'components/add_functions.php');
	$table = 'buildings'; # added so not to forget to change the insert AND audit
	$salt = makekey();
	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		building_name = '". encode($_POST['building_name']) ."',
		show_id = '". $_SESSION['show_id'] ."',
		access = ". ($_POST['access'] != '' ? "'".date('Y-m-d', strtotime($_POST['access']))."'" : "NULL") .",
		depart = ". ($_POST['depart'] != '' ? "'".date('Y-m-d', strtotime($_POST['depart']))."'" : "NULL") .",
		delivery_info = '". encode($_POST['delivery_info']) ."',
		notes = '". encode($_POST['notes']) ."',
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
			new_value='". encode($_POST['building_name']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Building: " . encode($_POST['building_name']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Building.');
	}
?>