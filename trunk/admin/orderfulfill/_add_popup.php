<?
	include_once($BF.'components/add_functions.php');
	include_once($BF.'components/edit_functions.php');

	$tmp_urproducts = db_query("SELECT products.id
							FROM products
							JOIN product_types ON products.producttype_id=product_types.id
							JOIN product_show ON product_show.product_id=products.id
							WHERE !products.deleted AND products.enabled AND !product_types.deleted AND product_types.enabled 
								AND product_show.show_id='".$_SESSION['show_id']."'
								AND products.id IN (SELECT product_sessiontypes.product_id FROM product_sessiontypes 
										WHERE product_sessiontypes.sessiontype_id='".$info['sessiontype_id']."') 
							ORDER BY product_types.product_type, products.common_name
						","Get User Requestable Products");
	$urproducts = array();
	while($row = mysqli_fetch_assoc($tmp_urproducts)) {
		$urproducts[] = $row['id'];
	}
	
	$tmp_datestimes = db_query('SELECT time_slots.id, time_slots.room_id
				FROM time_slots 
				JOIN rooms ON time_slots.room_id=rooms.id
				JOIN buildings ON rooms.building_id = buildings.id
				WHERE !time_slots.deleted AND class_id='.$info['id'].' 
				ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
				time_slots.description','Getting Dates and Times');
	$datetimes = array();
	while($row = mysqli_fetch_assoc($datetimes)) {
		$datetimes[] = $row['id'];
		$roomproducts[$row['id']] = array();
		$tmp_roomproducts = db_query("SELECT product_id 
									FROM room_products
									JOIN products AS P ON room_products.product_id=P.id
									WHERE !room_products.deleted AND !P.needs_quantity AND room_products.room_id='".$row['room_id']."'","Getting Room Products");
		while($row2 = mysqli_fetch_assoc($tmp_roomproducts)) {
			$roomproducts[$row['id']][] = $row2['id'];
		}
	}

	if($_POST['order_id'] == '') { // Add
		$q = "INSERT INTO session_orders SET 
				session_id='".$info['id']."',
				status_id='2',
				user_id='".$_SESSION['user_id']."',
				created_at=now(),updated_at=now()
		";
		if(db_query($q,"Insert Order")) {
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
			$setup_charge=0;
			if(isset($_POST['products'])) {
				$q2 = "";
				foreach($_POST['products'] AS $k => $id) {
					if($_POST['qty_'.$id] > 0) {
						if(isset($_POST['applyall_'.$id])) {
						 	foreach($datetimes AS $k2 => $id2) {
						 		if(!in_array($id,$roomproducts[$id2])) {
								 	$q2 .= "('".$id."','".$info['id']."','".$id2."','".$newID."','".$_POST['qty_'.$id]."','".$_SESSION['user_id']."','".$_POST['price_'.$id]."','".$_POST['setup_'.$id]."','1','".(!in_array($id,$urproducts)?'1':'0')."',now(),now()),";
								 }
							}
						} else {	
							$q2 .= "('".$id."','".$info['id']."','".$_POST['ts']."','".$newID."','".$_POST['qty_'.$id]."','".$_SESSION['user_id']."','".$_POST['price_'.$id]."','".$_POST['setup_'.$id]."','1','".(!in_array($id,$urproducts)?'1':'0')."',now(),now()),";
						}
					}
				}
				if($q2 != "") {
					db_query("INSERT INTO session_order_items (product_id, session_id, timeslot_id, order_id, quantity, user_id, price, setup, approved, added_by_admin, created_at, updated_at) VALUES ".substr($q2,0,-1),"Insert Products");
				}
			}
		}
		
		db_query("UPDATE session_orders SET 
					quote_name='".date('y')."TSMNECC-".$newID."',
					contingency='".($info['bill_other']?'10':'0')."',
					revision='1',
					updated_at=now()
					WHERE id='".$newID."'
		","Update Order");
		
	} else { // Update

		if(isset($_POST['products'])) {
			$q2 = "";
			foreach($_POST['products'] AS $k => $id) {
				if($_POST['qty_'.$id] > 0) {
					if(isset($_POST['applyall_'.$id])) {
					 	foreach($datetimes AS $k2 => $id2) {
					 		if(!in_array($id,$roomproducts[$id2])) {
					 			$test = db_query("SELECT id FROM session_order_items WHERE session_id='".$info['id']."' AND timeslot_id='".$id2."' AND product_id='".$id."' AND !deleted","Check for Product",1);
					 			if($test['id'] == "") {
								 	$q2 .= "('".$id."','".$info['id']."','".$id2."','".$_POST['order_id']."','".$_POST['qty_'.$id]."','".$_SESSION['user_id']."','".$_POST['price_'.$id]."','".$_POST['setup_'.$id]."','1','".(!in_array($id,$urproducts)?'1':'0')."',now(),now()),";
								}
							}
						}
					} else {	
			 			$test = db_query("SELECT id FROM session_order_items WHERE session_id='".$info['id']."' AND timeslot_id='".$_POST['ts']."' AND product_id='".$id."' AND !deleted","Check for Product",1);
			 			if($test['id'] == "") {
							$q2 .= "('".$id."','".$info['id']."','".$_POST['ts']."','".$_POST['order_id']."','".$_POST['qty_'.$id]."','".$_SESSION['user_id']."','".$_POST['price_'.$id]."','".$_POST['setup_'.$id]."','1','".(!in_array($id,$urproducts)?'1':'0')."',now(),now()),";
						}
					}
				}
			}
			if($q2 != "") {
				db_query("INSERT INTO session_order_items (product_id, session_id, timeslot_id, order_id, quantity, user_id, price, setup, approved, added_by_admin, created_at, updated_at) VALUES ".substr($q2,0,-1),"Insert Products");
			}
		}
	}

	if($_POST['moveTo'] == 'add') { //save
?>
	<html>
		<head>
			<script type='text/javascript'>
				window.opener.location.reload();
				window.location="add_popup.php?key=<?=$_POST['key']?>&ts=<?=$_POST['ts']?>"
			</script>
		</head>
	</html>
<?
	} else if($_POST['moveTo'] == 'addclose') { //save and close
?>
	<html>
		<head>
			<script type='text/javascript'>
				window.opener.location.reload();
				window.close();
			</script>
		</head>
	</html>
<?		
	} 
	die();
	
?>