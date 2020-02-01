<?
	include_once($BF.'components/add_functions.php');
	$table = 'rooms'; # added so not to forget to change the insert AND audit
	$salt = makekey();
	
	if($_POST['move_in-date'] != '') {
		$_POST['move_in'] = date('Y-m-d H:i:s', strtotime($_POST['move_in-date'].' '.$_POST['move_in-time']));
	} else {
		$_POST['move_in'] = "";
	}
	if($_POST['move_in-date'] != '') {
		$_POST['move_out'] = date('Y-m-d H:i:s', strtotime($_POST['move_out-date'].' '.$_POST['move_out-time']));
	} else {
		$_POST['move_out'] = "";
	}
	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		room_name = '". encode($_POST['room_name']) ."',
		room_number = '". encode($_POST['room_number']) ."',
		building_id = '". encode($_POST['building_id']) ."',
		show_id = '". $_SESSION['show_id'] ."',
		platform_id = '". encode($_POST['platform_id']) ."',
		internet_access = '". encode($_POST['internet_access']) ."',
		capacity = '". encode($_POST['capacity']) ."',
		square_feet = '". encode($_POST['square_feet']) ."',
		dimensions = '". encode($_POST['dimensions']) ."',
		public_location = '". encode($_POST['public_location']) ."',
		default_setup = '". encode($_POST['default_setup']) ."',
		category = '". encode($_POST['category']) ."',
		move_in = '". $_POST['move_in'] ."',
		move_out = '". $_POST['move_out'] ."',
		description = '". encode($_POST['description']) ."',
		notes = '". encode($_POST['notes']) ."',
		created_at = NOW(), updated_at = NOW()
	";
	
	# if there database insertion is successful	
	if(db_query($q,"Insert into ". $table)) {

		// This is the code for inserting the Audit Page
		// Type 1 means ADD NEW RECORD, change the TABLE NAME also
		global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
		$newID = mysqli_insert_id($mysqli_connection);

		$table2 = "room_files";
		$i = 0;
		$attachments = array();
		while ($i++ <  $_POST['intFiles']) {
			if($_FILES['chrFilesFile'.$i]['name'] != '') {
				$q = "INSERT INTO ". $table2 ." SET  
					room_id = '". $newID ."'
				";

			# if there database insertion is successful	
				if(db_query($q,"Insert into ". $table2)) {
					global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
					$newID2 = mysqli_insert_id($mysqli_connection);

					$attName = strtolower(str_replace(" ","_",basename($_FILES['chrFilesFile'.$i]['name'])));  //dtn: Replace any spaces with underscores.

					$uploaddir = $BF . 'files/rooms/'; 	//dtn: Setting up the directory name for where things go
				
					//dtn: Update the EmailMessages DB with the file attachment info.
					db_query("UPDATE ". $table2 ." SET 
						file_size = '". $_FILES['chrFilesFile'.$i]['size'] ."',
						file_name = '". $newID2 ."-". $attName ."',
						file_type = '". $_FILES['chrFilesFile'.$i]['type'] ."'
						WHERE ID=". $newID2 ."	
					","insert attachment");
		
					$uploadfile = $uploaddir . $newID2 .'-'. $attName;
				
					move_uploaded_file($_FILES['chrFilesFile'.$i]['tmp_name'], $uploadfile);  //dtn: move the file to where it needs to go.
					$attachments[] = $uploadfile;
				}
			}
		}



	
		$q = "INSERT INTO audit SET 
			audittype_id=1, 
			record_id='". $newID ."',
			new_value='". encode($_POST['room_name']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Room: " . encode($_POST['room_name']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Room.');
	}
?>