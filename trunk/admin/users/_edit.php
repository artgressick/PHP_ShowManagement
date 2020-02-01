<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'users';
	$mysqlStr = '';
	$audit = '';

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'first_name',$info['first_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'last_name',$info['last_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'email',$info['email'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'cellnumber',$info['cellnumber'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'mobile_carrier_id',$info['mobile_carrier_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'group_id',$info['group_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'enabled',$info['enabled'],$audit,$table,$info['id']);
	if($_POST['crypted_password'] != "" && $_POST['crypted_password2'] != "" && $_POST['crypted_password'] == $_POST['crypted_password2']) {
		$_POST['crypted_password'] = $_POST['crypted_password'].$info['salt'];
		list($mysqlStr,$audit) = set_strs_password($mysqlStr,'crypted_password',$info['crypted_password'],$audit,$table,$info['id']);
	}
	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '') { 
		$_SESSION['infoMessages'][] = $_POST['first_name']." ".$_POST['last_name']. " has been successfully updated in the Database.";
		list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
	 } else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['first_name']." ".$_POST['last_name'];
	 }
	
	header("Location: index.php");
	die();	
?>