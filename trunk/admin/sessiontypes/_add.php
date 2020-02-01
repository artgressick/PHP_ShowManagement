<?
	include_once($BF.'components/add_functions.php');
	$table = 'sessiontypes'; # added so not to forget to change the insert AND audit
	$salt = makekey();
	$q = "INSERT INTO ". $table ." SET 
		lkey = '". makekey() ."',
		sessiontype_name = '". encode($_POST['sessiontype_name']) ."',
		ignore_lock_date = '". $_POST['ignore_lock_date'] ."',
		created_at = NOW(), updated_at = NOW()
	";
	
	# if there database insertion is successful	
	if(db_query($q,"Insert into ". $table)) {

		// This is the code for inserting the Audit Page
		// Type 1 means ADD NEW RECORD, change the TABLE NAME also
		global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
		$newID = mysqli_insert_id($mysqli_connection);

		$producttypes = db_query("SELECT * FROM product_types WHERE !deleted ORDER BY product_type","Getting Product Type Names");
		$q2 = '';
		while($row = mysqli_fetch_assoc($producttypes)) {
			if(isset($_POST['allow'.$row['id']]) && $_POST['allow'.$row['id']] == "on") { 
				$q2 .= "('".$row['id']."','".$newID."','".(isset($_POST['custom'.$row['id']]) && $_POST['custom'.$row['id']] == "on" ? '1' : '0')."'),";
			}
		}
		
		if($q2 != "") {
			db_query("INSERT INTO sessiontype_producttypes (producttype_id,sessiontype_id,allowcustom) VALUES ".substr($q2,0,-1),"Insert Type Restrictions");
		}
	
		$q = "INSERT INTO audit SET 
			audittype_id=1, 
			record_id='". $newID ."',
			new_value='". encode($_POST['sessiontype_name']) ."',
			created_at=now(),
			table_name='". $table ."',
			user_id='". $_SESSION['user_id'] ."'
		";
		db_query($q,"Insert audit");
		//End the code for History Insert 
	
		$_SESSION['infoMessages'][] = "New Session Type: " . encode($_POST['sessiontype_name']) . " has been added";
		header("Location: ". $_POST['moveTo']);
		die();
	} else {
		# if the database insertion failed, send them to the error page with a useful message
		errorPage('An error has occurred while trying to add this Session Type.');
	}
?>