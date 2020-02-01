<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'product_types';
	$mysqlStr = '';
	$audit = '';

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'product_type',$info['product_type'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'enabled',$info['enabled'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'request_note',$info['request_note'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'custom_instructions',$info['custom_instructions'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'custom_top_instructions',$info['custom_top_instructions'],$audit,$table,$info['id']);

	if($_POST['typechanged'] == 1) {
		db_query("DELETE FROM sessiontype_producttypes WHERE producttype_id=".$info['id'],"Remove old Values");
		$sessiontypes = db_query("SELECT * FROM sessiontypes WHERE !deleted ORDER BY sessiontype_name","Getting Session Type Names");
		$q2 = '';
		while($row = mysqli_fetch_assoc($sessiontypes)) {
			if(isset($_POST['allow'.$row['id']]) && $_POST['allow'.$row['id']] == "on") { 
				$q2 .= "('".$info['id']."','".$row['id']."','".(isset($_POST['custom'.$row['id']]) && $_POST['custom'.$row['id']] == "on" ? '1' : '0')."'),";
			}
		}
		
		if($q2 != "") {
			db_query("INSERT INTO sessiontype_producttypes (producttype_id,sessiontype_id,allowcustom) VALUES ".substr($q2,0,-1),"Insert Session Type Restrictions");
		}
	}

	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '' || $_POST['typechanged']) { 
		$_SESSION['infoMessages'][] = $_POST['product_type']." has been successfully updated in the Database.";
		if($mysqlStr != '') {
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
		}
	 } else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['product_type'];
	 }
	
	header("Location: index.php");
	die();	
?>