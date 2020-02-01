<?
	include_once($BF.'components/add_functions.php');
	$table = 'product_types'; # added so not to forget to change the insert AND audit
	$salt = makekey();

	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		product_type = '". encode($_POST['product_type']) ."',
		enabled = '". $_POST['enabled'] ."',
		request_note = '". encode($_POST['request_note']) ."',
		custom_instructions = '". encode($_POST['custom_instructions']) ."',
		custom_top_instructions = '". encode($_POST['custom_top_instructions']) ."',
		created_at = NOW(), updated_at = NOW()
	";
	
	# if there database insertion is successful	
	if(db_query($q,"Insert into ". $table)) {

		// This is the code for inserting the Audit Page
		// Type 1 means ADD NEW RECORD, change the TABLE NAME also
		global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
		$newID = mysqli_insert_id($mysqli_connection);
	
		$sessiontypes = db_query("SELECT * FROM sessiontypes WHERE !deleted ORDER BY sessiontype_name","Getting Session Type Names");
		$q2 = '';
		while($row = mysqli_fetch_assoc($sessiontypes)) {
			if(isset($_POST['allow'.$row['id']]) && $_POST['allow'.$row['id']] == "on") { 
				$q2 .= "('".$newID."','".$row['id']."','".(isset($_POST['custom'.$row['id']]) && $_POST['custom'.$row['id']] == "on" ? '1' : '0')."'),";
			}
		}
		
		if($q2 != "") {
			db_query("INSERT INTO sessiontype_producttypes (producttype_id,sessiontype_id,allowcustom) VALUES ".substr($q2,0,-1),"Insert Session Type Restrictions");
		}

		$q = "INSERT INTO audit SET 
			audittype_id=1, 
			record_id='". $newID ."',
			new_value='". encode($_POST['product_type']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Product Type: " . encode($_POST['product_type']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Product Type.');
	}
?>