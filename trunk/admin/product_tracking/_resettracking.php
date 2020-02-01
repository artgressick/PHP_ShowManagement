<?
	include_once($BF.'components/add_functions.php');
	$table = 'product_tracking'; # added so not to forget to change the insert AND audit
	
	while($row = mysqli_fetch_assoc($results)) {
		if($_POST['product_id_'.$row['tracking_number']] != $row['product_id']) {
			db_query("UPDATE product_tracking SET product_id='".$_POST['product_id_'.$row['tracking_number']]."' WHERE tracking_number='".$row['tracking_number']."'","Update Tracking Record");
		}
	}
	mysqli_data_seek($results, 0);
	while($row = mysqli_fetch_assoc($results)) {
		if($_POST['otn_'.$row['tracking_number']] != $_POST['ntn_'.$row['tracking_number']]) {
			$test = db_query("SELECT product_id FROM product_tracking WHERE tracking_number='".$_POST['ntn_'.$row['tracking_number']]."'","Check for tn",1);
			if($test['product_id'] != "" && $test['product_id'] != $_POST['product_id_'.$row['tracking_number']]) {
				$_SESSION['errorMessages'][] = "Tracking Number already exists: ".$_POST['ntn_'.$row['tracking_number']];
			} else {
				db_query("UPDATE product_tracking SET tracking_number='".$_POST['ntn_'.$row['tracking_number']]."' WHERE tracking_number='".$row['tracking_number']."'","Change Tracking Number");
			}
		}
	}

	$_SESSION['infoMessages'][] = "Product Tracking Saved";
	header("Location: resettracking.php");
	die();
?>