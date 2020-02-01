<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'shows';
	$mysqlStr = '';
	$audit = '';

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'show_name',$info['show_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_date($mysqlStr,'start_date',$info['start_date'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_date($mysqlStr,'end_date',$info['end_date'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_date($mysqlStr,'lock_requests',$info['lock_requests'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_date($mysqlStr,'show_room_data',$info['show_room_data'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs_date($mysqlStr,'show_signoff',$info['show_signoff'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'status_id',$info['status_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'session_bill',$info['session_bill'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'landing_page',$info['landing_page'],$audit,$table,$info['id']);
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
	
	
	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '') { 
		$_SESSION['infoMessages'][] = $_POST['show_name']." has been successfully updated in the Database.";
		list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
	 } else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['show_name'];
	 }
	
	header("Location: index.php");
	die();	
?>