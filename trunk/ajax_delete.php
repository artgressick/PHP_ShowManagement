<?
	$BF = "";
	$NON_HTML_PAGE = true;
	require('_lib.php');
	if($_REQUEST['postType'] == "delete") {
		$total = 0;
		echo $q = "UPDATE ". $_REQUEST['tbl'] ." SET deleted=1, updated_at=NOW() WHERE id=".$_REQUEST['id'];
		if(db_query($q,"update deleted")) { 
			$total++;
			$q = "INSERT INTO audit SET user_id=".$_REQUEST['user_id'].", record_id=".$_REQUEST['id'].", table_name='". $_REQUEST['tbl'] ."', column_name='deleted', created_at=now(), 
					old_value='0', new_value='1', audittype_id=3"; 
			if(db_query($q,"insert into audit")) { $total += 2; }
		}
  		echo $total;
	} else if(@$_REQUEST['postType'] == "permDelete") {
		$total = 0;
		$q = "DELETE FROM ". $_REQUEST['tbl'] ." WHERE id=".$_REQUEST['id'];
		if(db_query($q,"perm delete")) { 
			$total++;
			$q = "INSERT INTO audit SET user_id=".$_REQUEST['user_id'].", record_id=".$_REQUEST['id'].", table_name='". $_REQUEST['tbl'] ."', column_name='', created_at=now(), 
					old_value='', new_value='', audittype_id=4"; 
			if(db_query($q,"insert into audit table")) { $total += 2; }
		}
  		echo $total;
	} else if(@$_REQUEST['postType'] == "sessioninfo") {
		//display Session Information for calendar view
		$q = 'SELECT classes.*, sessiontypes.sessiontype_name,
				CONCAT("<table cellpadding=\'2\' cellspacing=\'0\' class=\'sessiondatestimes\'><tr><th>Date</th><th>Prep</th><th>Start Time</th><th>End Time</th><th>Strike</th><th>Room Name</th></tr>",
						(SELECT GROUP_CONCAT(CONCAT("<tr><td>",DATE_FORMAT(time_slots.start_date,"%a. %b. %e"),"</td><td>",
						DATE_FORMAT(time_slots.prep_time,"%l:%i %p"),"</td><td>",
						DATE_FORMAT(time_slots.start_time,"%l:%i %p"),"</td><td>",
						DATE_FORMAT(time_slots.end_time,"%l:%i %p"),"</td><td>",
						DATE_FORMAT(time_slots.strike_time,"%l:%i %p"),"</td><td>",
						rooms.room_name,"</td></tr>")
						ORDER BY time_slots.start_date,time_slots.start_time,time_slots.end_time SEPARATOR "") 
						FROM time_slots 
						JOIN rooms ON time_slots.room_id=rooms.id
						WHERE !time_slots.deleted AND time_slots.class_id=classes.id AND !rooms.deleted GROUP BY time_slots.class_id),"</table>") AS date_times
				FROM classes
				JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
				WHERE classes.id='.$_REQUEST['id'];

		$data = db_query($q,"Get Information",1); 
		echo 'JSONdata = {"session_name":"'.$data['class_name'].'",
          "session_number":"'.$data['class_number'].'",
          "session_type":"'.$data['sessiontype_name'].'",
          "speaker":"'.$data['speaker'].'",
          "date_times":"'.$data['date_times'].'"}';
  	} else if($_REQUEST['postType'] == "updatequantity") {
		$total = 0;
		$q = "UPDATE ".$_REQUEST['tbl']." SET quantity=" . $_REQUEST['quantity'] ." WHERE id=" . $_REQUEST['id'];
		if(db_query($q,'update quantity')) { $total++; }
		echo $total;
  	} else if($_REQUEST['postType'] == "checkforquantityneeded") {
		$q = "SELECT needs_quantity FROM products WHERE id=" . $_REQUEST['id'];
		$value = db_query($q,"Getting Value",1);
		echo $value['needs_quantity'];
	} else if($_REQUEST['postType'] == "product_price") {
		$q = "SELECT price FROM products WHERE id=" . $_REQUEST['id'];
		$value = db_query($q,"Getting Value",1);
		echo $value['price'];
	} else if($_REQUEST['postType'] == "product_setup") {
		$q = "SELECT setup_fee FROM products WHERE id=" . $_REQUEST['id'];
		$value = db_query($q,"Getting Value",1);
		echo $value['setup_fee'];
	} else if($_REQUEST['postType'] == "text_tech") {
		include_once('includes/_emailer.php');
//		if(emailer('jsummers@techitsolutions.com','Assistance Needed',$_REQUEST['msg'].$_REQUEST['tfrom'],$_REQUEST['tfrom'])) {
		if(emailer($_REQUEST['tto'],'Assistance Needed',$_REQUEST['msg'],$_REQUEST['tfrom'],'','drone@techitsolutions.com')) {
			echo "true";
		} else {
			echo "false";
		}
	} else if($_REQUEST['postType'] == 'checkout') {

		$id_check = db_query("SELECT product_id, CONCAT(products.product_name,' (',products.common_name,')') AS product_name
		FROM product_tracking
		JOIN products ON product_tracking.product_id=products.id 
		WHERE tracking_number = '".encode($_REQUEST['tracking'])."' 
			AND show_id = '".$_SESSION['show_id']."'
			ORDER BY check_in
		","Getting First Product ID",1);

		if($id_check['product_id'] == '' || $id_check['product_id'] == $_REQUEST['product_id']) {
			$check = db_query("SELECT product_tracking.*, rooms.room_name, buildings.building_name, CONCAT(products.product_name,' (',products.common_name,')') AS product_name
				FROM product_tracking
				JOIN rooms ON product_tracking.room_id=rooms.id
				JOIN buildings ON rooms.building_id=buildings.id 
				JOIN products ON product_tracking.product_id=products.id 
				WHERE product_tracking.show_id='".$_SESSION['show_id']."' 
				AND product_tracking.tracking_number='".encode($_REQUEST['tracking'])."' 
				AND product_tracking.check_in IS NULL
				","Checking for product checkout",1);
			if($check['id'] == '') {
		
				$q = "INSERT INTO product_tracking SET 
						room_id = '".$_REQUEST['room_id']."',
						product_id = '".$_REQUEST['product_id']."',
						user_id = '".$_SESSION['user_id']."',
						show_id = '".$_SESSION['show_id']."',
						check_out = '".date('Y-m-d h:i:00')."',
						tracking_number = '".encode($_REQUEST['tracking'])."'
						";
				if(db_query($q,"Insert Tracking")) {
					echo "<table class='infMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>Product Checked Out Successfully. Tracking #: ".$_REQUEST['tracking']."</td></tr></table>";
				} else {
					echo "<table class='errMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>An error has occurred while checking out Tracking #: ".$_REQUEST['tracking']."</td></tr></table>";
				}
			} else {
				echo "<table class='errMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>Tracking Number \"".$_REQUEST['tracking']."\" is already checked into room: ".$check['room_name']." (".$check['building_name'].") on ".pretty_datetime($check['check_out'])." - Product: ".$check['product_name']."</td></tr></table>";
			}
		} else {
			$product = db_query("SELECT CONCAT(products.product_name,' (',products.common_name,')') AS product_name FROM products WHERE id='".$_REQUEST['product_id']."'","Get Product Name",1);
			echo "<table class='errMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>Tracking Number: ". $_REQUEST['tracking']." is assigned to product \"".$id_check['product_name']."\" not \"".$product['product_name']."\". Check-out Failed</td></tr></table>";
		}
	} else if($_REQUEST['postType'] == 'checkin') {
	
		$check = db_query("SELECT product_tracking.*, CONCAT(products.product_name,' (',products.common_name,')') AS product_name
			FROM product_tracking
			JOIN products ON product_tracking.product_id=products.id
			WHERE product_tracking.show_id='".$_SESSION['show_id']."' 
			AND product_tracking.tracking_number='".encode($_REQUEST['tracking'])."' AND check_in IS NULL
			","Checking for product checkout",1);
		if($check['id'] != '') {
			$q = "UPDATE product_tracking SET check_in='".date('Y-m-d h:i:00')."', check_in_by='".$_SESSION['user_id']."' WHERE id='".$check['id']."'";
	
			if(db_query($q,"Check In Product")) {

				echo "<table class='infMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>Product \"".$check['product_name']."\" Checked In Successfully. Tracking #: ".$_REQUEST['tracking']."</td></tr></table>";

			} else {

				echo "<table class='errMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>An error has occurred while checking in Tracking #: ".$_REQUEST['tracking']."</td></tr></table>";

			}

		} else {

			echo "<table class='errMessage' cellpadding='0' cellspacing='0'><tr><td class='icon'><!-- Icon --></td><td class='msg'>Tracking Number \"".$_REQUEST['tracking']."\" is not Checked-Out</td></tr></table>";

		}

	} else if($_REQUEST['postType'] == 'getassetdata') {
		//First lets grab all assets in room, list of product, type, and tracking number
		$assets1 = db_query("SELECT product_tracking.*, CONCAT(products.product_name,' (',products.common_name,')') AS product_name, product_types.product_type
							FROM product_tracking
							JOIN products ON product_tracking.product_id=products.id 
							JOIN product_types ON products.producttype_id=product_types.id
							WHERE product_tracking.show_id='".$_SESSION['show_id']."' AND product_tracking.room_id='".$_REQUEST['room_id']."' 
								AND product_tracking.check_in IS NULL
							ORDER BY product_name
		","Get All Assets");
		
		//Now we need to could the assets based off of product name
		$assets2 = db_query("SELECT products.id, COUNT(product_tracking.id) AS product_count, CONCAT(products.product_name,' (',products.common_name,')') AS product_name, product_types.product_type
							FROM product_tracking
							JOIN products ON product_tracking.product_id=products.id 
							JOIN product_types ON products.producttype_id=product_types.id
							WHERE product_tracking.show_id='".$_SESSION['show_id']."' AND product_tracking.room_id='".$_REQUEST['room_id']."' 
								AND product_tracking.check_in IS NULL
							GROUP BY product_tracking.product_id
							ORDER BY product_name
		","Get asset counts grouped by product");
		
		//Now lets start echoing out the layout for the page
?>
	<table cellpadding="0" cellspacing="0" style="width:100%;">
		<tr>
			<td style="width:49%; vertical-align:top;">
				<div class="half_colHeader">Assets Assigned to Room</div>
<?
			if(mysqli_num_rows($assets1) > 0) {
?>
				<table cellpadding="0" cellspacing="0" class="List half_width">
					<thead>
						<tr>
							<th>Tracking Number</th>
							<th>Product</th>
							<th>Product Type</th>
						</tr>
					</thead>
					<tbody>
<?
					$i = 1;
					while($row = mysqli_fetch_assoc($assets1)) {
?>					
						<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
							<td><?=$row['tracking_number']?></td>
							<td><?=$row['product_name']?></td>
							<td><?=$row['product_type']?></td>
						</tr>
<?
						$i++;
					}
?>
					</tbody>
				</table>
<?			
			} else {
?>
				<div style="padding:10px;">No Assets in this Room</div>
<?			
			}
?>
			</td>
			<td style="width:2%;">&nbsp;</td>
			<td style="width:49%; vertical-align:top;">
				<div class="half_colHeader">Assets Totals for Room</div>
<?
			if(mysqli_num_rows($assets2) > 0) {
?>
				<table cellpadding="0" cellspacing="0" class="List half_width">
					<thead>
						<tr>
							<th>Qty</th>
							<th>Product</th>
							<th>Product Type</th>
						</tr>
					</thead>
					<tbody>
<?
					$i = 1;
					while($row = mysqli_fetch_assoc($assets2)) {
?>					
						<tr class="<?=($i%2 ? 'ListOdd' : 'ListEven')?>">
							<td><?=$row['product_count']?></td>
							<td><?=$row['product_name']?></td>
							<td><?=$row['product_type']?></td>
						</tr>
<?
						$i++;
					}
?>
					</tbody>
				</table>
<?			
			} else {
?>
				<div style="padding:10px;">No Assets in this Room</div>
<?			
			}
?>

			</td>
		</tr>
	</table>
<?
		
	
	
	}
	

?>