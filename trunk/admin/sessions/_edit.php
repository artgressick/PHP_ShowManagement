<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'classes';
	$mysqlStr = '';
	$audit = '';

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'class_name',$info['class_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'sessioncat_id',$info['sessioncat_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'sessiontype_id',$info['sessiontype_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'audience_size',$info['audience_size'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'class_number',$info['class_number'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'speaker',$info['speaker'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'room_area',$info['room_area'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'description',$info['description'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'notes',$info['notes'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_other',$info['bill_other'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_name',$info['bill_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_address1',$info['bill_address1'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_address2',$info['bill_address2'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_address3',$info['bill_address3'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_local',$info['bill_local'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_state',$info['bill_state'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_postal',$info['bill_postal'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_country',$info['bill_country'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_phone',$info['bill_phone'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_fax',$info['bill_fax'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'bill_email',$info['bill_email'],$audit,$table,$info['id']);

	
	if($_POST['dtupdated'] == 1) {
		db_query("DELETE FROM time_slots WHERE !deleted AND class_id=".$info['id'],"Remove old dates and times");
		$i = 0;
		$q2 = "";
		while($i++ < $time_slots) {
			if($_POST['date_slot_'.$i] != "") {
			
				if($_POST['prep_time_slot_'.$i] == "") { $_POST['prep_time_slot_'.$i] = $_POST['start_time_slot_'.$i]; }
				if($_POST['strike_time_slot_'.$i] == "") { $_POST['strike_time_slot_'.$i] = $_POST['end_time_slot_'.$i]; }

				$q2 .= "(".$info['id'].",'".date('Y-m-d',strtotime($_POST['date_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['prep_time_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['start_time_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['end_time_slot_'.$i]))."','".date('H:i:s',strtotime($_POST['strike_time_slot_'.$i]))."','".encode($_POST['description_'.$i])."','".$_POST['room_id'.$i]."'),";
			}
		}
		if($q2 != "") {
			db_query("INSERT INTO time_slots (class_id, start_date, prep_time, start_time, end_time, strike_time, description, room_id) VALUES ".substr($q2,0,-1),"Insert Times");
		}
	}
	
		$table2 = "files";
		$i = 0;
		$filesuploaded=0;
		$attachments = array();
		while ($i++ <  $_POST['intFiles']) {
			if($_FILES['chrFilesFile'.$i]['name'] != '') {
				$q = "INSERT INTO ". $table2 ." SET  
					class_id = '". $info['id'] ."'
				";

			# if there database insertion is successful	
				if(db_query($q,"Insert into ". $table2)) {
					$filesuploaded=1;
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

	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '' || $_POST['dtupdated'] == 1 || $filesuploaded == 1) { 
		$_SESSION['infoMessages'][] = $_POST['class_name']." has been successfully updated in the Database.";
		if($mysqlStr != '') { 
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
		}
	 } else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['class_name'];
	 }
	
	header("Location: index.php");
	die();	
?>