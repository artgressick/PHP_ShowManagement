<?
	include_once($BF.'components/edit_functions.php');
	// Set the basic values to be used.
	//   $table = the table that you will be connecting to to check / make the changes
	//   $mysqlStr = this is the "mysql string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	//   $sudit = this is the "audit string" that you are going to be using to update with.  This needs to be set to "" (empty string)
	$table = 'products';
	$mysqlStr = '';
	$audit = '';

	// "List" is a way for php to split up an array that is coming back.  
	// "set_strs" is a function (bottom of the _lib) that is set up to look at the old information in the DB, and compare it with
	//    the new information in the form fields.  If the information is DIFFERENT, only then add it to the mysql string to update.
	//    This will ensure that only information that NEEDS to be updated, is updated.  This means smaller and faster DB calls.
	//    ...  This also will ONLY add changes to the audit table if the values are different.
	list($mysqlStr,$audit) = set_strs($mysqlStr,'product_name',$info['product_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'common_name',$info['common_name'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'vendor_id',$info['vendor_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'producttype_id',$info['producttype_id'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'enabled',$info['enabled'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'track_product',$info['track_product'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'exclude',$info['exclude'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'needs_quantity',$info['needs_quantity'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'price',$info['price'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'setup_fee',$info['setup_fee'],$audit,$table,$info['id']);
	list($mysqlStr,$audit) = set_strs($mysqlStr,'cost',$info['cost'],$audit,$table,$info['id']);
	
	
	$other_updated = false;
	if($_POST['updated_platforms'] == 1) {

		$other_updated = true;

		db_query("DELETE FROM product_platforms WHERE product_id=".$info['id'],"Remove old values");

		if(isset($_POST['platforms']) && count($_POST['platforms']) > 0 ) {
			$q2 = "";
			foreach($_POST['platforms'] AS $k => $v) {
				$q2 .= "('".$info['id']."','".$v."'),";
			}
			if($q2 != "") { db_query("INSERT INTO product_platforms (product_id, platform_id) VALUES ".substr($q2,0,-1),"Insert Platforms"); }
		}
	}
	if($_POST['updated_sessiontypes'] == 1) {
	
		$other_updated = true;
	
		db_query("DELETE FROM product_sessiontypes WHERE product_id=".$info['id'],"Remove old values");
	
		if(isset($_POST['sessiontypes']) && count($_POST['sessiontypes']) > 0 ) {
			$q2 = "";
			foreach($_POST['sessiontypes'] AS $k => $v) {
				$q2 .= "('".$info['id']."','".$v."'),";
			}
			if($q2 != "") { db_query("INSERT INTO product_sessiontypes (product_id, sessiontype_id) VALUES ".substr($q2,0,-1),"Insert Session Types"); }
		}

	}
	if($_POST['updated_shows'] == 1) {

		$other_updated = true;

		db_query("DELETE FROM product_show WHERE product_id=".$info['id'],"Remove old values");

		if(isset($_POST['shows']) && count($_POST['shows']) > 0 ) {
			$q2 = "";
			foreach($_POST['shows'] AS $k => $v) {
				$q2 .= "('".$info['id']."','".$v."'),";
			}
			if($q2 != "") { db_query("INSERT INTO product_show (product_id, show_id) VALUES ".substr($q2,0,-1),"Insert Shows"); }
		}
	}
	
	
	
	// if nothing has changed, don't do anything.  Otherwise update / audit.
	if($mysqlStr != '' || $other_updated == true) { 
		$_SESSION['infoMessages'][] = $_POST['product_name']." has been successfully updated in the Database.";
		if($mysqlStr != '') { 
			list($str,$aud) = update_record($mysqlStr, $audit, $table, $info['id']);
		}
	 } else {
	 	$_SESSION['infoMessages'][] = "No Changes have been made to ".$_POST['product_name'];
	 }
	
	header("Location: index.php");
	die();	
?>