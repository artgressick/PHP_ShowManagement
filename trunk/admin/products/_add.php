<?
	include_once($BF.'components/add_functions.php');
	$table = 'products'; # added so not to forget to change the insert AND audit
	$salt = makekey();
	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		common_name = '". encode($_POST['common_name']) ."',
		product_name = '". encode($_POST['product_name']) ."',
		producttype_id = '". encode($_POST['producttype_id']) ."',
		vendor_id = '". encode($_POST['vendor_id']) ."',
		enabled = '". encode($_POST['enabled']) ."',
		track_product = '". encode($_POST['track_product']) ."',
		exclude = '". encode($_POST['exclude']) ."',
		needs_quantity = '". encode($_POST['needs_quantity']) ."',
		price = '". $_POST['price'] ."',
		setup_fee = '". $_POST['setup_fee'] ."',
		cost = '". $_POST['cost'] ."',
		created_at = NOW(), updated_at = NOW()
	";
	
	# if there database insertion is successful	
	if(db_query($q,"Insert into ". $table)) {

		// This is the code for inserting the Audit Page
		// Type 1 means ADD NEW RECORD, change the TABLE NAME also
		global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
		$newID = mysqli_insert_id($mysqli_connection);

		if(isset($_POST['platforms']) && count($_POST['platforms']) > 0 ) {
			$q2 = "";
			foreach($_POST['platforms'] AS $k => $v) {
				$q2 .= "('".$newID."','".$v."'),";
			}
			if($q2 != "") { db_query("INSERT INTO product_platforms (product_id, platform_id) VALUES ".substr($q2,0,-1),"Insert Platforms"); }
		}

		if(isset($_POST['sessiontypes']) && count($_POST['sessiontypes']) > 0 ) {
			$q2 = "";
			foreach($_POST['sessiontypes'] AS $k => $v) {
				$q2 .= "('".$newID."','".$v."'),";
			}
			if($q2 != "") { db_query("INSERT INTO product_sessiontypes (product_id, sessiontype_id) VALUES ".substr($q2,0,-1),"Insert Session Types"); }
		}

		if(isset($_POST['shows']) && count($_POST['shows']) > 0 ) {
			$q2 = "";
			foreach($_POST['shows'] AS $k => $v) {
				$q2 .= "('".$newID."','".$v."'),";
			}
			if($q2 != "") { db_query("INSERT INTO product_show (product_id, show_id) VALUES ".substr($q2,0,-1),"Insert Shows"); }
		}


	
		$q = "INSERT INTO audit SET 
			audittype_id=1, 
			record_id='". $newID ."',
			new_value='". encode($_POST['product_name']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Product: " . encode($_POST['product_name']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Products.');
	}
?>