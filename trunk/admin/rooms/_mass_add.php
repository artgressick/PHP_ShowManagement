<?
	include_once($BF.'components/add_functions.php');
	$table = 'room_products'; # added so not to forget to change the insert AND audit

	if(isset($_POST['products']) && count($_POST['products']) > 0) {
		if(isset($_POST['rooms']) && count($_POST['rooms']) > 0) {
			$q2 = "";
			foreach($_POST['products'] AS $k => $product_id) {
				foreach($_POST['rooms'] AS $j => $room_id) {
					$check = db_query("SELECT id FROM ".$table." WHERE !deleted AND room_id='".$room_id."' AND product_id='".$product_id."'","Check for product",1);
					if($check['id'] == "") {
						$q2 .= "('".$room_id."','".$product_id."','".$_POST['qty'.$product_id]."',now(),now()),";
					}
				}
			}
			if($q2 != "") {
				db_query("INSERT INTO ".$table." (room_id,product_id,quantity,created_at,updated_at) VALUES ".substr($q2,0,-1),"Insert Products");
			}
		} else {
			errorPage('No Rooms where Selected.');
		}
	} else {
		errorPage('No Products where Selected.');
	}

	$_SESSION['infoMessages'][] = "Product(s) Added to Room(s) Successfully";
	header("Location: index.php");
	die();
?>