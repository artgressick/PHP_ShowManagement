<?
	include_once($BF.'components/add_functions.php');
	include_once($BF.'components/edit_functions.php');

		$ignorelock = db_query("SELECT id FROM sessiontypes WHERE id='".$info['sessiontype_id']."' AND ignore_lock_date","Get Session Types for lock ignore",1);
		
		
		$allow_ordering = false;
		if(date('Y-m-d') < $show_data['lock_requests']) { $allow_ordering = true; }
		if($_SESSION['user_id'] != 1 || $ignorelock['id'] == $info['sessiontype_id']) { $allow_ordering = true; }


	if($_POST['order_id'] == '') { // Add
		$q = "INSERT INTO session_orders SET 
				session_id='".$info['id']."',
				user_id='".$_SESSION['user_id']."',
				created_at=now(),updated_at=now()
		";
		if(isset($_POST['sign_off_by']) && strlen($_POST['sign_off_by']) >= 2) {
			$q .= ", sign_off_by='".encode($_POST['sign_off_by'])."', sign_off_date=now()
			";
		}
		if(db_query($q,"Insert Order")) {
			global $mysqli_connection;  // This is needed for mysqli to be able to get the "last insert id"
			$newID = mysqli_insert_id($mysqli_connection);
			$order_status = ($info['bill_other']?'1':'2'); //pending
			$item_status = ($info['bill_other']?'0':'1');

			//make an array for products and its pricing
			$qproducts = db_query("SELECT * FROM products WHERE !deleted","Get Products");
			$products = array();
			while($row = mysqli_fetch_assoc($qproducts)) {
				$products[$row['id']] = array('setup'=>$row['setup_fee'],'price'=>$row['price']);
			}

			$datestimes = db_query('SELECT time_slots.*, rooms.room_name, buildings.building_name 
							FROM time_slots 
							JOIN rooms ON time_slots.room_id=rooms.id
							JOIN buildings ON rooms.building_id = buildings.id
							WHERE !time_slots.deleted AND class_id='.$info['id'].' 
							ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
							time_slots.description','Getting Dates and Times');
			while($row = mysqli_fetch_assoc($datestimes)) {
				//First do the products
				$q2 = "";
				if(isset($_POST['product_ids_'.$row['id']]) && count($_POST['product_ids_'.$row['id']])>0) { // Start the insert of products
					foreach($_POST['product_ids_'.$row['id']] AS $k => $id) {
						if(isset($_POST['qty_'.$row['id'].'_'.$id]) && is_numeric($_POST['qty_'.$row['id'].'_'.$id]) && $_POST['qty_'.$row['id'].'_'.$id] > 0) {
							$q2 .= "('".$item_status."','".$id."','".$info['id']."','".$row['id']."','".$newID."','".$_POST['qty_'.$row['id'].'_'.$id]."','".$_SESSION['user_id']."','".$products[$id]['price']."','".$products[$id]['setup']."',NOW(),NOW()),";
						}
					}
				}
				if($q2 != "") {
					db_query("INSERT INTO session_order_items (approved,product_id,session_id,timeslot_id,order_id,quantity,user_id,price,setup,created_at,updated_at) 
								VALUES ".substr($q2,0,-1),"Insert Products");
				}
				
				//Now do any custom requests
				$q2 = "";
				$producttypes = db_query("SELECT product_types.*, sp.allowcustom
										FROM product_types
										JOIN sessiontype_producttypes AS sp ON product_types.id=sp.producttype_id 
											AND sp.sessiontype_id='".$info['sessiontype_id']."'
										WHERE !product_types.deleted AND product_types.enabled 
										ORDER BY product_type
											","getting product types");
				while($pt = mysqli_fetch_assoc($producttypes)) {
					if(isset($_POST['custom_'.$row['id'].'_'.$pt['id']]) && strlen($_POST['custom_'.$row['id'].'_'.$pt['id']]) > 2) {
						$q2 .= "('1','".$newID."','".$row['id']."','".$pt['id']."','".$_SESSION['user_id']."','".encode($_POST['custom_'.$row['id'].'_'.$pt['id']])."',NOW(),NOW()),";
						$order_status = 1;
					}
				}
				
				if(isset($_POST['itemsbeingbrought'.$row['id']]) && strlen($_POST['itemsbeingbrought'.$row['id']]) > 2) {
					$q2 .= "('3','".$newID."','".$row['id']."','','".$_SESSION['user_id']."','".encode($_POST['itemsbeingbrought'.$row['id']])."',now(),now()),";
				}

				if($q2 != "") {
					db_query("INSERT INTO session_notes (note_type_id,order_id,timeslot_id,producttype_id,user_id,note,created_at,updated_at) 
								VALUES ".substr($q2,0,-1),"Insert Custom Requests");
				}
				
				// Answers to dynamic Questions
				$q2 = "";
				$tmp_question = db_query("SELECT pt_questions.*, answers.id AS answer_id, answers.answer
								FROM pt_questions 
								LEFT JOIN answers ON pt_questions.id=answers.question_id AND answers.timeslot_id='".$row['id']."' AND answers.order_id='".$orderinfo['id']."'
								WHERE !pt_questions.deleted AND pt_questions.show_id='".$_SESSION['show_id']."'
								ORDER BY pt_questions.sort_order, pt_questions.question							
							",'Getting Question');
				while($q = mysqli_fetch_assoc($tmp_question)) {
					//First lets make the answer easier to work with

					if($q['fieldtype_id'] != 1 && isset($_POST['answer_'.$row['id'].'_'.$q['id']])) { 
						if($q['fieldtype_id'] == 2 || $q['fieldtype_id'] == 3 || $q['fieldtype_id'] == 4) {
							$q2 .= "('". $newID ."','". $row['id'] ."','".$q['id']."','". encode($_POST['answer_'.$row['id'].'_'.$q['id']]) ."'),";
						} else if($q['fieldtype_id'] == 5) {
							$tmp_options = explode('|||',$q['options']);
							$i = 0;
							$len = count($tmp_options);
							$ans = "";
							while($i < $len) {
								if($_POST['answer_'.$row['id'].'_'.$q['id']] != '' && $i == $_POST['answer_'.$row['id'].'_'.$q['id']]) { $ans = $tmp_options[$i]; break; }
								$i++;
							}
							$q2 .= "('". $newID ."','". $row['id'] ."','".$q['id']."','". $ans ."'),";
						} else {
							$tmp_options = explode('|||',$q['options']);
							$i = 0;
							$len = count($tmp_options);
							$ans = "";
							while($i < $len) {
								if(in_array($i,$_POST['answer_'.$row['id'].'_'.$q['id']])) { $ans .= $tmp_options[$i].", "; }
								$i++;
							}
							$q2 .= "('". $newID ."','". $row['id'] ."','".$q['id']."','". substr($ans,0,-2) ."'),";
						}
					} 
				}
				if($q2 != "") {
						db_query("INSERT INTO answers (order_id,timeslot_id,question_id,answer) 
								VALUES ".substr($q2,0,-1),"Insert Answers");
				}
			}
			
			db_query("UPDATE session_orders SET 
						status_id='".$order_status."',
						quote_name='".date('y')."TSMNECC-".$newID."',
						contingency='".($info['bill_other']?'10':'0')."',
						revision='1',
						updated_at=now()
						WHERE id='".$newID."'
			","Update Order");
			
			//Now lets do the order notes
			$q2 = "";

			if(isset($_POST['ordernote']) && strlen($_POST['ordernote']) > 2) {
				$q2 .= "('2','".$newID."','".$_SESSION['user_id']."','".encode($_POST['ordernote'])."',now(),now()),";
			}
			if($q2 != "") {
				db_query("INSERT INTO session_notes (note_type_id,order_id,user_id,note,created_at,updated_at) 
								VALUES ".substr($q2,0,-1),"Insert Other Information");
			}
			$_SESSION['infoMessages'][] = "Order Saved. Order Number: ".date('y')."TSMNECC-".$newID;
		} else {
			# if the database insertion failed, send them to the error page with a useful message
			errorPage('An error has occurred while trying to add this Order.');
		}

	} else { // Update

	if($allow_ordering) {
	
		$datestimes = db_query('SELECT time_slots.*, rooms.room_name, buildings.building_name 
						FROM time_slots 
						JOIN rooms ON time_slots.room_id=rooms.id
						JOIN buildings ON rooms.building_id = buildings.id
						WHERE !time_slots.deleted AND class_id='.$info['id'].' 
						ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
						time_slots.description','Getting Dates and Times');
		while($row = mysqli_fetch_assoc($datestimes)) {
			//put together all ids
			if(isset($_POST['product_ids_'.$row['id']])) {
				$temp_all_product_ids = implode(',',$_POST['product_ids_'.$row['id']]);
			} else {
				$temp_all_product_ids = "";
			}

			//make an array for products and its pricing
			$qproducts = db_query("SELECT * FROM products WHERE !deleted","Get Products");
			$products = array();
			while($prow = mysqli_fetch_assoc($qproducts)) {
				$products[$prow['id']] = array('setup'=>$prow['setup_fee'],'price'=>$prow['price']);
			}


			db_query("UPDATE session_order_items SET 
						deleted=1,updated_at=now()
						WHERE !deleted AND timeslot_id='".$row['id']."' AND order_id='".$orderinfo['id']."' AND !added_by_admin".($temp_all_product_ids != ""?" AND product_id NOT IN (".$temp_all_product_ids.")":""),"Remove products unchecked");
						
			$existing_products = array();
			if($temp_all_product_ids != "") {
				$grab_ids = db_query("SELECT * FROM session_order_items 
				WHERE !deleted AND timeslot_id='".$row['id']."' AND order_id='".$orderinfo['id']."' 
					AND !added_by_admin AND product_id IN (".$temp_all_product_ids.")","Get all info");
				while($p = mysqli_fetch_assoc($grab_ids)) {
					$existing_products[$p['product_id']] = $p;
				}
			}

			$item_status = ($info['bill_other']?'0':'1');				
			$order_changed = false;
			$custom_changed = false;
			if(isset($_POST['product_ids_'.$row['id']])) {
				foreach($_POST['product_ids_'.$row['id']] AS $k => $id) {
					if(isset($existing_products[$id]) 
							&& $existing_products[$id]['quantity'] != $_POST['qty_'.$row['id'].'_'.$id] 
							&& $_POST['qty_'.$row['id'].'_'.$id] > 0 && is_numeric($_POST['qty_'.$row['id'].'_'.$id])) { //update
							
						db_query("UPDATE session_order_items SET 
									quantity='".$_POST['qty_'.$row['id'].'_'.$id]."',
									approved='".$item_status."',
									updated_at=now()
									WHERE id='".$existing_products[$id]['id']."'
						","Update ".$existing_products[$id]['id']);
						$order_changed = "true";
					} else if(!isset($existing_products[$id]['quantity']) && $_POST['qty_'.$row['id'].'_'.$id] > 0 
						&& is_numeric($_POST['qty_'.$row['id'].'_'.$id])) { //insert
						
						db_query("INSERT INTO session_order_items SET 
									approved = '".$item_status."',
									product_id = '".$id."',
									session_id = '".$info['id']."',
									timeslot_id = '".$row['id']."',
									order_id = '".$orderinfo['id']."',
									quantity = '".$_POST['qty_'.$row['id'].'_'.$id]."',
									user_id = '".$_SESSION['user_id']."',
									price = '".$products[$id]['price']."',
									setup = '".$products[$id]['setup']."',
									created_at=now(),
									updated_at=now()
						","Insert ".$id);
						
						$order_changed = "true";
					} else if(isset($existing_products[$id]) 
							&& $existing_products[$id]['quantity'] != $_POST['qty_'.$row['id'].'_'.$id] 
							&& $_POST['qty_'.$row['id'].'_'.$id] == 0 && !is_numeric($_POST['qty_'.$row['id'].'_'.$id])) { //remove
	
						db_query("UPDATE session_order_items SET 
									delete='1',
									updated_at=now()
									WHERE id='".$existing_products[$id]['id']."'
						","Update ".$existing_products[$id]['id']);
						
						$order_changed = "true";
					}
				}
			}
			// Now lets grab and check the custom order fields
			$producttypes = db_query("SELECT product_types.*, sp.allowcustom
						FROM product_types
						JOIN sessiontype_producttypes AS sp ON product_types.id=sp.producttype_id 
							AND sp.sessiontype_id='".$info['sessiontype_id']."'
						WHERE !product_types.deleted AND product_types.enabled 
						ORDER BY product_type
							","getting product types");
			$temp_custom = db_query("SELECT * FROM session_notes WHERE
									note_type_id=1 AND order_id='".$orderinfo['id']."' AND timeslot_id='".$row['id']."'","get all custom orders");
			
			$current_custom_orders = array();
			while($cn = mysqli_fetch_assoc($temp_custom)) {
				$current_custom_orders[$cn['producttype_id']] = $cn;
			}

			while($pt = mysqli_fetch_assoc($producttypes)) {
				//Now to just do all the checks for changes, update, insert or remove
				if(isset($_POST['custom_'.$row['id'].'_'.$pt['id']]) && !isset($current_custom_orders[$pt['id']]) && strlen($_POST['custom_'.$row['id'].'_'.$pt['id']]) > 2 ) { // Insert
					db_query("INSERT INTO session_notes SET 
							note_type_id=1,
							order_id='".$orderinfo['id']."',
							timeslot_id='".$row['id']."',
							producttype_id='".$pt['id']."',
							user_id='".$_SESSION['user_id']."',
							note='".encode($_POST['custom_'.$row['id'].'_'.$pt['id']])."',
							created_at=now(),updated_at=now()
							","Insert Custom Order");
					$custom_changed = true;
				} else if(isset($_POST['custom_'.$row['id'].'_'.$pt['id']]) && isset($current_custom_orders[$pt['id']]) && strlen($_POST['custom_'.$row['id'].'_'.$pt['id']]) > 2 && $_POST['custom_'.$row['id'].'_'.$pt['id']] != $current_custom_orders[$pt['id']]['note']) { // Update
					
					db_query("UPDATE session_notes SET 
							note='".encode($_POST['custom_'.$row['id'].'_'.$pt['id']])."',
							updated_at=now()
							WHERE id='".$current_custom_orders[$pt['id']]['id']."'
					","Update Custom Order");
				
					$custom_changed = true;
				} else if (isset($_POST['custom_'.$row['id'].'_'.$pt['id']]) && isset($current_custom_orders[$pt['id']]) && strlen($_POST['custom_'.$row['id'].'_'.$pt['id']]) <= 2 && $_POST['custom_'.$row['id'].'_'.$pt['id']] != $current_custom_orders[$pt['id']]['note']) { // clear
					db_query("UPDATE session_notes SET 
							note='',
							updated_at=now()
							WHERE id='".$current_custom_orders[$pt['id']]['id']."'
					","Update Custom Order");
				
					$custom_changed = true;
				}
			}
			
			$tmp_other_notes = db_query("SELECT * FROM session_notes WHERE
										note_type_id = 3 AND timeslot_id='".$row['id']."' AND order_id='".$orderinfo['id']."'","get all other note info",1);
			
			if(isset($_POST['itemsbeingbrought'.$row['id']]) && strlen($_POST['itemsbeingbrought'.$row['id']]) > 2 && $tmp_other_notes['id'] == "") { // insert
				db_query("INSERT INTO session_notes SET 
							note_type_id='3', 
							order_id='".$orderinfo['id']."',
							timeslot_id='".$row['id']."',
							user_id='".$_SESSION['user_id']."',
							note='".encode($_POST['itemsbeingbrought'.$row['id']])."',
							created_at=now(),updated_at=now()","Insert Brought Items");
			} else if (isset($_POST['itemsbeingbrought'.$row['id']]) && strlen($_POST['itemsbeingbrought'.$row['id']]) > 2 && $tmp_other_notes['id'] != "" && $_POST['itemsbeingbrought'.$row['id']] != $tmp_other_notes['note']) {
				db_query("UPDATE session_notes SET 
							note='".$_POST['itemsbeingbrought'.$row['id']]."',
							updated_at=now()
							WHERE id='".$tmp_other_notes['id']."'","Update Items being brought");		
			} else if (isset($_POST['itemsbeingbrought'.$row['id']]) && strlen($_POST['itemsbeingbrought'.$row['id']]) <= 2 && $tmp_other_notes['id'] != "" && $_POST['itemsbeingbrought'.$row['id']] != $tmp_other_notes['note']) {
				db_query("UPDATE session_notes SET 
							note='',
							updated_at=now()
							WHERE id='".$tmp_other_notes['id']."'","Update Items being brought");		
			}

				// Answers to dynamic Questions
				$q2 = "";
				$tmp_question = db_query("SELECT pt_questions.*, answers.id AS answer_id, answers.answer
								FROM pt_questions 
								LEFT JOIN answers ON pt_questions.id=answers.question_id AND answers.timeslot_id='".$row['id']."' AND answers.order_id='".$orderinfo['id']."'
								WHERE !pt_questions.deleted AND pt_questions.show_id='".$_SESSION['show_id']."'
								ORDER BY pt_questions.sort_order, pt_questions.question							
							",'Getting Question');
				while($q = mysqli_fetch_assoc($tmp_question)) {
					//First lets make the answer easier to work with

					if($q['fieldtype_id'] != 1 && isset($_POST['answer_'.$row['id'].'_'.$q['id']]) && $q['answer_id']=='') { // Insert
						if($q['fieldtype_id'] == 2 || $q['fieldtype_id'] == 3 || $q['fieldtype_id'] == 4) {
							$q2 .= "('". $orderinfo['id'] ."','". $row['id'] ."','".$q['id']."','". encode($_POST['answer_'.$row['id'].'_'.$q['id']]) ."'),";
						} else if($q['fieldtype_id'] == 5) {
							$tmp_options = explode('|||',$q['options']);
							$i = 0;
							$len = count($tmp_options);
							$ans = "";
							while($i < $len) {
								if($_POST['answer_'.$row['id'].'_'.$q['id']] != '' && $i == $_POST['answer_'.$row['id'].'_'.$q['id']]) { $ans = $tmp_options[$i]; break; }
								$i++;
							}
							$q2 .= "('". $orderinfo['id'] ."','". $row['id'] ."','".$q['id']."','". $ans ."'),";
						} else {
							$tmp_options = explode('|||',$q['options']);
							$i = 0;
							$len = count($tmp_options);
							$ans = "";
							while($i < $len) {
								if(in_array($i,$_POST['answer_'.$row['id'].'_'.$q['id']])) { $ans .= $tmp_options[$i].", "; }
								$i++;
							}
							$q2 .= "('". $orderinfo['id'] ."','". $row['id'] ."','".$q['id']."','". substr($ans,0,-2) ."'),";
						}
					} else if ($q['fieldtype_id'] != 1 && isset($_POST['answer_'.$row['id'].'_'.$q['id']]) && $q['answer_id']!='') { //Update
						
						if($q['fieldtype_id'] == 2 || $q['fieldtype_id'] == 3 || $q['fieldtype_id'] == 4) {
							if($_POST['answer_'.$row['id'].'_'.$q['id']] != $q['answer']) {
								db_query("UPDATE answers SET 
											answer='".encode($_POST['answer_'.$row['id'].'_'.$q['id']])."'
											WHERE answers.id='".$q['answer_id']."'",'Update Answer');
							}
						} else if($q['fieldtype_id'] == 5) {
							$tmp_options = explode('|||',$q['options']);
							$i = 0;
							$len = count($tmp_options);
							$ans = "";
							while($i < $len) {
								if($_POST['answer_'.$row['id'].'_'.$q['id']] != '' && $i == $_POST['answer_'.$row['id'].'_'.$q['id']]) { $ans = $tmp_options[$i]; break; }
								$i++;
							}
							if($ans != $q['answer']) {
								db_query("UPDATE answers SET 
											answer='".$ans."'
											WHERE answers.id='".$q['answer_id']."'",'Update Answer');
							}
						} else {
							$tmp_options = explode('|||',$q['options']);
							$i = 0;
							$len = count($tmp_options);
							$ans = "";
							while($i < $len) {
								if(in_array($i,$_POST['answer_'.$row['id'].'_'.$q['id']])) { $ans .= $tmp_options[$i].", "; }
								$i++;
							}
							if(substr($ans,0,-2) != $q['answer']) {
								db_query("UPDATE answers SET 
											answer='".substr($ans,0,-2)."'
											WHERE answers.id='".$q['answer_id']."'",'Update Answer');
							}
						}
						
						
					} 
				}
				if($q2 != "") {
						db_query("INSERT INTO answers (order_id,timeslot_id,question_id,answer) 
								VALUES ".substr($q2,0,-1),"Insert Answers");
				}



		}
		
		
		
		}
		$tmp_other_notes = db_query("SELECT * FROM session_notes WHERE
									note_type_id = 2 AND order_id='".$orderinfo['id']."'","get all other note info",1);
		
		if(isset($_POST['ordernote']) && strlen($_POST['ordernote']) > 2 && $tmp_other_notes['id'] == "") { // insert
			db_query("INSERT INTO session_notes SET 
						note_type_id='2', 
						order_id='".$orderinfo['id']."',
						user_id='".$_SESSION['user_id']."',
						note='".encode($_POST['ordernote'])."',
						created_at=now(),updated_at=now()","Insert Order Notes");
		} else if (isset($_POST['ordernote']) && strlen($_POST['ordernote']) > 2 && $tmp_other_notes['id'] != "" && $_POST['ordernote'] != $tmp_other_notes['note']) {
			db_query("UPDATE session_notes SET 
						note='".$_POST['ordernote']."',
						updated_at=now()
						WHERE id='".$tmp_other_notes['id']."'","Update Order Note");		
		} else if (isset($_POST['ordernote']) && strlen($_POST['ordernote']) <= 2 && $tmp_other_notes['id'] != "" && $_POST['ordernote'] != $tmp_other_notes['note']) {
			db_query("UPDATE session_notes SET 
						note='',
						updated_at=now()
						WHERE id='".$tmp_other_notes['id']."'","Update Order Note");		
		}

		
		// Update Order
		$signoff = '';
		if(isset($_POST['sign_off_by']) && strlen($_POST['sign_off_by']) >= 2) {
			$signoff = ", sign_off_by='".encode($_POST['sign_off_by'])."', sign_off_date=now()
			";
		}

		db_query("UPDATE session_orders SET 
					status_id = '".(($order_changed && $info['bill_other']) || $custom_changed ? '1' : $orderinfo['status_id'])."',
					revision = '".(($order_changed && $info['bill_other']) || $custom_changed ? ($orderinfo['revision']+1) : $orderinfo['revision'])."',
					updated_at = NOW()".$signoff."
					WHERE id='".$orderinfo['id']."'
		
		","Update Order");
		
		
		$_SESSION['infoMessages'][] = "Order Saved. Order Number: ".$orderinfo['quote_name'];
	}

	header("Location: order.php?".$_POST['query_string']);
	die();

?>