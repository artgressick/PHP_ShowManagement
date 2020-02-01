<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/orderfulfill/','TEXT'=>"Order Fulfillment");
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Order List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin1','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['status_id']) || $_REQUEST['status_id'] == '') { $_REQUEST['status_id'] = "1"; }

			$q = "SELECT classes.*, sessiontypes.sessiontype_name, IF(!classes.bill_other,'Main','Other') as billto,
						(SELECT CONCAT(start_date,' ',start_time) as start_dt FROM time_slots WHERE !time_slots.deleted AND class_id=classes.id ORDER BY start_date, prep_time, start_time, end_time, strike_time LIMIT 1) as start_dt, 
						(SELECT COUNT(time_slots.id) FROM time_slots WHERE !time_slots.deleted AND class_id=classes.id) AS time_count, so.quote_name,status.order_status
					FROM classes
					JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
					LEFT JOIN session_orders AS so ON so.session_id=classes.id
					LEFT JOIN order_status as status ON status.id=so.status_id
					WHERE !classes.deleted AND classes.show_id='".$_SESSION['show_id']."'
					". (isset($_REQUEST['status_id']) && is_numeric($_REQUEST['status_id']) ? " AND so.status_id='".$_REQUEST['status_id']."'": "" ) ."
					ORDER BY start_dt, classes.class_name";

			$results = db_query($q,"getting Order data");
			
			
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "";
			}

			$status = db_query("SELECT id, order_status AS name FROM order_status ","Get Status");
	
			$filter = array();
			while($row = mysqli_fetch_assoc($status)) {
				$filter[$row['id']] = $row['name'];
			}
			$filter['all'] = "All";
			

			$page_info['title'] = "Order Fulfillment";
			$page_info['instructions'] = "Status Filter: ".form_select($filter,array('caption'=>'- Select Order Status-','nocaption'=>'true','name'=>'status_id','value'=>$_REQUEST['status_id'],'extra'=>'onchange="location.href=\'?status_id=\'+this.value"','style'=>'width:100px;'));
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Order Page
		#################################################
		case 'order.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			//If the person is passed from ISTE's website we need to do some work first

			$breadcrumbs[] = array('TEXT' => "Session Products");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin1','1');
			include_once($BF.'components/formfields.php');
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name
								FROM classes
								JOIN sessioncats on classes.sessioncat_id = sessioncats.id
								JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
								WHERE classes.lkey='". $_REQUEST['key'] ."' AND !classes.deleted
			","getting info",1); // Get Info
			
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?

			$show_data = db_query("SELECT * FROM shows WHERE id=".$_SESSION['show_id'],"Getting Show Information",1);
			
			//Lets see if there is already a order for this session/person
			$orderinfo = db_query("SELECT session_orders.*, order_status.order_status 
			FROM session_orders 
			JOIN order_status ON session_orders.status_id=order_status.id 
			WHERE session_orders.session_id='".$info['id']."'","getting order info",1);

			if(isset($_POST['Save'])) { include($post_file); }

			if($info['bill_other']) {
				$bodyParams = "calculate_total_bill();";
			}

			# Stuff In The Header
			function sith() { 
				global $BF,$info;
				include($BF .'components/list/sortlistjs.php');
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?

?>	<script type='text/javascript'>
		function check_product(id) {
			if(document.getElementById('product_'+id).checked==true) {
				document.getElementById('qty_'+id).value=1;
				if(document.getElementById('qty_'+id).type == 'text') {
					document.getElementById('qty_'+id).disabled=false;
					document.getElementById('qty_'+id).focus();
					document.getElementById('approved_'+id).disabled=false;
				} else {
					document.getElementById('approved_'+id).disabled=false;
				}
				if(document.getElementById('price_'+id)) {
					document.getElementById('price_'+id).disabled=false;
					document.getElementById('setup_'+id).disabled=false;
				}

			} else {
				document.getElementById('qty_'+id).value='';
				if(document.getElementById('qty_'+id).type == 'text') {
					document.getElementById('qty_'+id).disabled=true;
					document.getElementById('approved_'+id).checked=false;
					document.getElementById('approved_'+id).disabled=true;
				} else {
					document.getElementById('approved_'+id).checked=false;
					document.getElementById('approved_'+id).disabled=true;
				}
				if(document.getElementById('price_'+id)) {
					document.getElementById('price_'+id).disabled=true;
					document.getElementById('setup_'+id).disabled=true;
				}
			}
<?
		if($info['bill_other']) {
?>		
			calculate_total_bill();
<?
		}
?>
		} 
<?
		if($info['bill_other']) {
?>		
		function calculate_total_bill() {
			var grand_total = 0;
			var product_total = 0;
			var setup_total = 0;
			if(!IsWhole(document.getElementById('contingency').value)) { document.getElementById('contingency').value = 10; }
			var cont = (parseInt(document.getElementById('contingency').value) / 100);
			var cont_total = 0;
			
			var all_ids = document.getElementById('product_ids').value;
			if(all_ids != "") {
				var ids = all_ids.split(","); 
				for ( var i in ids ) {
				    if(document.getElementById('product_'+ids[i])) {
				    	if(document.getElementById('product_'+ids[i]).checked == true && document.getElementById('approved_'+ids[i]).checked == true) {
				    		var product_id = document.getElementById('product_'+ids[i]).value;
				    		if(!IsWhole(document.getElementById('qty_' + ids[i]).value)) { document.getElementById('qty_' + ids[i]).value = 1; }
				    		if(!IsNumeric(document.getElementById('price_' + ids[i]).value)) { getProductPrice('<?=$BF?>',product_id,ids[i]); }
				    		if(!IsNumeric(document.getElementById('setup_' + ids[i]).value)) { getProductSetup('<?=$BF?>',product_id,ids[i]); }
				    	
							var tprice = (+document.getElementById('price_' + ids[i]).value) * parseInt(document.getElementById('qty_' + ids[i]).value);
							var tsetup = (+document.getElementById('setup_' + ids[i]).value) * parseInt(document.getElementById('qty_' + ids[i]).value);
							
							var ttotal = tprice + tsetup;
							
							product_total = product_total + tprice;
							
							setup_total = setup_total + tsetup;
				    	
				    	}
				    }
				}
			}
			document.getElementById('product_charge').innerHTML = product_total.toFixed(2);
			document.getElementById('setup_charge').innerHTML = setup_total.toFixed(2);
			cont_total = ((product_total + setup_total) * cont);
			cont_total = (+cont_total).toFixed(2);
			document.getElementById('cont_charge').innerHTML = cont_total;
			grand_total = ((+product_total) + (+setup_total) + (+cont_total));
			document.getElementById('total_charge').innerHTML = grand_total.toFixed(2);
		
		}
<?
		}
?>

	</script>
<?

			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			# The template to use (should be the last thing before the break)
			
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Popup Page
		#################################################
		case 'add_popup.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Order List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin1','1');
			include_once($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name
								FROM classes
								JOIN sessioncats on classes.sessioncat_id = sessioncats.id
								JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
								WHERE classes.lkey='". $_REQUEST['key'] ."' AND !classes.deleted
			","getting info",1); // Get Info
			
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?

			$show_data = db_query("SELECT * FROM shows WHERE id=".$_SESSION['show_id'],"Getting Show Information",1);
			
			//Lets see if there is already a order for this session/person
			$orderinfo = db_query("SELECT session_orders.*, order_status.order_status 
			FROM session_orders 
			JOIN order_status ON session_orders.status_id=order_status.id 
			WHERE session_orders.session_id='".$info['id']."'","getting order info",1);

			if(isset($_POST['moveTo'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
?>	<script type='text/javascript'>
		function check_product(id) {
			if(document.getElementById('product_'+id).checked==true) {
				document.getElementById('qty_'+id).value=1;
				if(document.getElementById('price_'+id)) {
					document.getElementById('price_'+id).disabled=false;
					document.getElementById('setup_'+id).disabled=false;
				}
				if(document.getElementById('applyall_'+id)) {
					document.getElementById('applyall_'+id).disabled=false;
				}
				if(document.getElementById('qty_'+id).type == 'text') {
					document.getElementById('qty_'+id).disabled=false;
					document.getElementById('qty_'+id).focus();
				}
			} else {
				document.getElementById('qty_'+id).value='';
				if(document.getElementById('price_'+id)) {
					document.getElementById('price_'+id).disabled=true;
					document.getElementById('setup_'+id).disabled=true;
				}
				if(document.getElementById('applyall_'+id)) {
					document.getElementById('applyall_'+id).disabled=true;
				}
				if(document.getElementById('qty_'+id).type == 'text') {
					document.getElementById('qty_'+id).disabled=true;
				}
			}
		} 

	</script>
<?

			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "";
			}

		
			# The template to use (should be the last thing before the break)
			include($BF ."models/popup.php");		
			
			break;

		#################################################
		##	View More Page
		#################################################
		case 'view_more.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin1','1');
			include_once($BF.'components/formfields.php');
			
			$info = db_query("SELECT * FROM product_types WHERE id='".$_REQUEST['pt']."'","Get Product Type",1);

			$q = "SELECT products.id, CONCAT(IF(room_products.quantity>1,CONCAT('(',room_products.quantity,' x) '),''),products.common_name) AS products
												FROM room_products
												JOIN products ON room_products.product_id=products.id
												JOIN product_types ON products.producttype_id=product_types.id
												JOIN rooms ON room_products.room_id=rooms.id
												WHERE products.enabled AND !room_products.deleted AND !products.deleted 
													AND rooms.lkey='".$_REQUEST['key']."' AND product_types.id='".$_REQUEST['pt']."'
												ORDER BY products.common_name";
				
			$results = db_query($q,"getting classes");
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "products";
			}

			# The template to use (should be the last thing before the break)
			include($BF ."models/popup.php");		
			
			break;

		#################################################
		##	PDF Order Page
		#################################################
		case 'pdf_order.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin1','1');
			include_once($BF.'components/formfields.php');
			
			if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'email') { $_REQUEST['action'] = 'view'; }
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name
								FROM classes
								JOIN sessioncats on classes.sessioncat_id = sessioncats.id
								JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
								WHERE classes.lkey='". $_REQUEST['key'] ."' AND !classes.deleted
			","getting info",1); // Get Info
			
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?

			$show_data = db_query("SELECT * FROM shows WHERE id=".$_SESSION['show_id'],"Getting Show Information",1);
			
			//Lets see if there is already a order for this session/person
			$orderinfo = db_query("SELECT session_orders.*, order_status.order_status 
			FROM session_orders 
			JOIN order_status ON session_orders.status_id=order_status.id 
			WHERE session_orders.session_id='".$info['id']."'","getting order info",1);
				
			break;

		#################################################
		##	Excel Order Page
		#################################################
		case 'excel_order.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin1','1');
			include_once($BF.'components/formfields.php');
			
			if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'email') { $_REQUEST['action'] = 'view'; }
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name
								FROM classes
								JOIN sessioncats on classes.sessioncat_id = sessioncats.id
								JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
								WHERE classes.lkey='". $_REQUEST['key'] ."' AND !classes.deleted
			","getting info",1); // Get Info
			
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?

			$show_data = db_query("SELECT * FROM shows WHERE id=".$_SESSION['show_id'],"Getting Show Information",1);
			
			//Lets see if there is already a order for this session/person
			$orderinfo = db_query("SELECT session_orders.*, order_status.order_status 
			FROM session_orders 
			JOIN order_status ON session_orders.status_id=order_status.id 
			WHERE session_orders.session_id='".$info['id']."'","getting order info",1);
				
			break;
		#################################################
		##	Else show Error Page
		#################################################
		default:
			include($BF .'_lib.php');
			errorPage('Page Incomplete.  Please notify an Administrator that you have received this error.');
	}

?>