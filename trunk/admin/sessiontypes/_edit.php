<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'sessiontypes';
	$mysqlStr = '';
	$audit = '';

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'sessiontype_name',$info['sessiontype_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'ignore_lock_date',$info['ignore_lock_date'],$audit,$table,$info['id']);
	
	if($_POST['typechanged'] == 1) {
		db_query("DELETE FROM sessiontype_producttypes WHERE sessiontype_id=".$info['id'],"Remove old Values");
		$producttypes = db_query("SELECT * FROM product_types WHERE !deleted ORDER BY product_type","Getting Product Type Names");
		$q2 = '';
		while($row = mysqli_fetch_assoc($producttypes)) {
			if(isset($_POST['allow'.$row['id']]) && $_POST['allow'.$row['id']] == "on") { 
				$q2 .= "('".$row['id']."','".$info['id']."','".(isset($_POST['custom'.$row['id']]) && $_POST['custom'.$row['id']] == "on" ? '1' : '0')."'),";
			}
		}
		
		if($q2 != "") {
			db_query("INSERT INTO sessiontype_producttypes (producttype_id,sessiontype_id,allowcustom) VALUES ".substr($q2,0,-1),"Insert Session Type Restrictions");
		}
	}

	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '' || $_POST['typechanged']) { 
		$_SESSION['infoMessages'][] = $_POST['sessiontype_name']." has been successfully updated in the Database.";
		if($mysqlStr != '') {
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
		}
	} else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['sessiontype_name'];
	}
	
	header("Location: index.php");
	die();	
?>