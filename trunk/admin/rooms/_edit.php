<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'rooms';
	$mysqlStr = '';
	$audit = '';

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

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'room_name',$info['room_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'room_number',$info['room_number'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'building_id',$info['building_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'platform_id',$info['platform_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'internet_access',$info['internet_access'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'capacity',$info['capacity'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'square_feet',$info['square_feet'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'dimensions',$info['dimensions'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'public_location',$info['public_location'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'default_setup',$info['default_setup'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'category',$info['category'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_datetime($mysqlStr,'move_in',$info['move_in'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_datetime($mysqlStr,'move_out',$info['move_out'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'description',$info['description'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'notes',$info['notes'],$audit,$table,$info['id']);


		$table2 = "room_files";
		$i = 0;
		$filesuploaded=0;
		$attachments = array();
		while ($i++ <  $_POST['intFiles']) {
			if($_FILES['chrFilesFile'.$i]['name'] != '') {
				$q = "INSERT INTO ". $table2 ." SET  
					room_id = '". $info['id'] ."'
				";

			# if there database insertion is successful	
				if(db_query($q,"Insert into ". $table2)) {
					$filesuploaded=1;
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


	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '') { 
		$_SESSION['infoMessages'][] = $_POST['room_name']." has been successfully updated in the Database.";
		list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
	 } else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['room_name'];
	 }
	
	header("Location: index.php");
	die();	
?>