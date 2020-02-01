<?
	include_once($BF.'components/add_functions.php');
	$table = 'classes'; # added so not to forget to change the insert AND audit
	$salt = makekey();
	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		class_name = '". encode($_POST['class_name']) ."',
		show_id = '". $_SESSION['show_id'] ."',
		status_id = '3',
		sessioncat_id = '". encode($_POST['sessioncat_id']) ."',
		sessiontype_id = '". encode($_POST['sessiontype_id']) ."',
		audience_size = '". encode($_POST['audience_size']) ."',
		class_number = '". encode($_POST['class_number']) ."',
		speaker = '". encode($_POST['speaker']) ."',
		room_area = '". encode($_POST['room_area']) ."',
		description = '". encode($_POST['description']) ."',
		notes = '". encode($_POST['notes']) ."',
		bill_other = '". $_POST['bill_other'] ."',
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
		$i = 0;
		$q2 = "";
		while($i++ < $time_slots) {
			if($_POST['date_slot_'.$i] != "") {
			
				if($_POST['prep_time_slot_'.$i] == "") { $_POST['prep_time_slot_'.$i] = $_POST['start_time_slot_'.$i]; }
				if($_POST['strike_time_slot_'.$i] == "") { $_POST['strike_time_slot_'.$i] = $_POST['end_time_slot_'.$i]; }

				$q2 .= "(".$newID.",'".date('Y-m-d',strtotime($_POST['date_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['prep_time_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['start_time_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['end_time_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['strike_time_slot_'.$i]))."','".encode($_POST['description_'.$i])."','".$_POST['room_id'.$i]."'),";
			}
		}
		if($q2 != "") {
			db_query("INSERT INTO time_slots (class_id, start_date, prep_time, start_time, end_time, strike_time, description, room_id) VALUES ".substr($q2,0,-1),"Insert Times");
		}


		$table2 = "files";
		$i = 0;
		$attachments = array();
		while ($i++ <  $_POST['intFiles']) {
			if($_FILES['chrFilesFile'.$i]['name'] != '') {
				$q = "INSERT INTO ". $table2 ." SET  
					class_id = '". $newID ."'
				";

			# if there database insertion is successful	
				if(db_query($q,"Insert into ". $table2)) {
					global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
					$newID2 = mysqli_insert_id($mysqli_connection);

					$attName = strtolower(str_replace(" ","_",basename($_FILES['chrFilesFile'.$i]['name'])));  //dtn: Replace any spaces with underscores.

					$uploaddir = $BF . 'files/sessions/'; 	//dtn: Setting up the directory name for where things go
				
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
			new_value='". encode($_POST['class_name']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Session: " . encode($_POST['class_name']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Session.');
	}
?>