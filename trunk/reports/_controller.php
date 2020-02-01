<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Reports";
	$breadcrumbs[] = array('TEXT' => "Reports");
    
	switch($file_name[0]) {
		#################################################
		##	Rooms with Internet Report
		#################################################
		case 'rooms_internet.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Rooms with Internet Report");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }

			$q = "SELECT rooms.*, buildings.building_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !buildings.deleted AND !rooms.deleted AND rooms.internet_access AND rooms.show_id = ".$_SESSION['show_id']."
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					ORDER BY rooms.room_name, buildings.building_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}
				
			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id'],"Get Buildings");
			
			$page_info['title'] = "Rooms with Internet Report";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?building_id=\'+this.value"','style'=>'width:100px;'));
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;
		#################################################
		##	Room Access Report
		#################################################
		case 'rooms_access.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Room Access Times");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }

			$q = "SELECT rooms.*, buildings.building_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !buildings.deleted AND !rooms.deleted AND rooms.show_id = ".$_SESSION['show_id']."
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					ORDER BY rooms.room_name, buildings.building_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id'],"Get Buildings");

			$page_info['title'] = "Room Access Times";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?building_id=\'+this.value"','style'=>'width:100px;'));

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Rooms Checked In
		#################################################
		case 'rooms_checked.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Rooms Checked In");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }

			if(isset($_POST['idReset']) && $_POST['idReset'] != '') { 
				db_query("UPDATE rooms SET checked_datetime=NULL, checked_user_id=NULL WHERE id='".$_POST['idReset']."'","Reset Checkin");
			}
			if(isset($_POST['rcheckoff']) && $_POST['rcheckoff'] != '') { 
				db_query("UPDATE rooms SET checked_datetime='".date('Y-m-d H:i:00')."', checked_user_id='".$_SESSION['user_id']."' WHERE id='".$_POST['rcheckoff']."'","Check In Room");
			}



			$q = "SELECT rooms.*, buildings.building_name, CONCAT(users.first_name,' ', users.last_name) as full_name,
					IF(rooms.checked_datetime != '',1,0) as checked
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					LEFT JOIN users ON rooms.checked_user_id=users.id
					WHERE !buildings.deleted AND !rooms.deleted AND rooms.show_id = ".$_SESSION['show_id']."
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					ORDER BY checked, rooms.room_name, buildings.building_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id'],"Get Buildings");

			$page_info['title'] = "Room Checkin Report";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?building_id=\'+this.value"','style'=>'width:100px;'));

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Room Products Report
		#################################################
		case 'room_products.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Room Products");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
			if(!isset($_REQUEST['producttype_id'])) { $_REQUEST['producttype_id'] = ""; }
			if(!isset($_REQUEST['product_id'])) { $_REQUEST['product_id'] = ""; }
			if(!isset($_REQUEST['show_vendor']) || $_SESSION['group_id'] != 1 ) { $_REQUEST['show_vendor'] = 0; }

			$q = "SELECT rooms.*, buildings.building_name, room_products.quantity, products.product_name, products.common_name, product_types.product_type, vendors.vendor_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN room_products ON room_products.room_id=rooms.id
					JOIN products ON room_products.product_id=products.id
					JOIN product_types ON product_types.id=products.producttype_id
					JOIN vendors ON products.vendor_id=vendors.id
					WHERE !buildings.deleted AND !products.exclude AND !rooms.deleted AND rooms.show_id = ".$_SESSION['show_id']." AND !room_products.deleted AND !products.deleted AND products.enabled AND !product_types.deleted and product_types.enabled
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND product_types.id='".$_REQUEST['producttype_id']."'": "" ) ."
					". (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id']) ? " AND products.id='".$_REQUEST['product_id']."'": "" ) ."

					ORDER BY rooms.room_name, buildings.building_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");
			
			$product_types = db_query("SELECT id, product_type AS name FROM product_types WHERE !deleted AND enabled ORDER BY name","Get Product Types");
			
			$products = db_query("SELECT id, CONCAT(product_name,' (',common_name,')') AS name FROM products WHERE !deleted AND !products.exclude AND enabled".(isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" )." ORDER BY name","Get Products");
			
			$page_info['title'] = "Room Products";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".
			
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?room_id=&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&room_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($product_types,array('caption'=>'All Product Types','nocaption'=>'true','name'=>'producttype_id','value'=>$_REQUEST['producttype_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&product_id='.($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&producttype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($products,array('caption'=>'All Products','nocaption'=>'true','name'=>'product_id','value'=>$_REQUEST['product_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&product_id=\'+this.value"','style'=>'width:100px;'));

			if($_SESSION['group_id'] == 1) {
				$page_info['instructions'] .= '&nbsp;&nbsp;&nbsp;<input type="checkbox" name="show_vendor" id="show_vendor"'.(isset($_REQUEST['show_vendor']) && $_REQUEST['show_vendor'] == 1 ? ' checked="checked"':'').' value="1" onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&show_vendor='.($_REQUEST['show_vendor'] == 1?'0':'1').'\'" /> <label for="show_vendor">Show Vendor</label>';
			}

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Session Products Report
		#################################################
		case 'session_products.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Session Products");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['class_id'])) { $_REQUEST['class_id'] = ""; }
			if(!isset($_REQUEST['sessiontype_id'])) { $_REQUEST['sessiontype_id'] = ""; }
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
			if(!isset($_REQUEST['producttype_id'])) { $_REQUEST['producttype_id'] = ""; }
			if(!isset($_REQUEST['product_id'])) { $_REQUEST['product_id'] = ""; }
			if(!isset($_REQUEST['show_vendor']) || $_SESSION['group_id'] != 1 ) { $_REQUEST['show_vendor'] = 0; }

			$tempdates = db_query("SELECT time_slots.start_date
								FROM time_slots
								JOIN classes ON time_slots.class_id=classes.id
								WHERE !classes.deleted AND !time_slots.deleted
								GROUP BY time_slots.start_date
								ORDER BY time_slots.start_date
								","Get dates");
			
			$dates = array();
			while($row = mysqli_fetch_assoc($tempdates)) {
				$dates[$row['start_date']] = pretty_date($row['start_date']);
			}
			
			
			if(!isset($_REQUEST['date']) || !array_key_exists($_REQUEST['date'], $dates)) { 
				$today = date('Y-m-d');
				if(!array_key_exists($today,$dates)) {
					mysqli_data_seek($tempdates,0);
					$temp = mysqli_fetch_assoc($tempdates);
					$_REQUEST['date'] = $temp['start_date'];
				} else {
					$_REQUEST['date'] = $today;
				}
			
			}



			$q = "SELECT classes.*, CONCAT(session_orders.quote_name,'-',revision) AS jobcode, soi.quantity,	
					CONCAT(products.product_name,' (',common_name,')') as product_name, product_types.product_type, rooms.room_name, 
					rooms.description AS rdescription, buildings.building_name, vendors.vendor_name, time_slots.start_date, time_slots.start_time, 
					time_slots.end_time, sessiontypes.sessiontype_name
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN session_orders ON classes.id=session_orders.session_id
					JOIN session_order_items AS soi ON session_orders.id=soi.order_id
					JOIN products ON soi.product_id=products.id
					JOIN product_types ON products.producttype_id=product_types.id
					JOIN time_slots ON soi.timeslot_id=time_slots.id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN vendors ON products.vendor_id=vendors.id
					WHERE !classes.deleted AND session_orders.status_id=2 AND !soi.deleted AND soi.approved AND !time_slots.deleted 
						AND !rooms.deleted AND !products.deleted AND !products.exclude AND products.enabled AND !product_types.deleted and product_types.enabled
						AND classes.show_id = ".$_SESSION['show_id']." AND time_slots.start_date='".$_REQUEST['date']."'
					". (isset($_REQUEST['class_id']) && is_numeric($_REQUEST['class_id']) ? " AND classes.id='".$_REQUEST['class_id']."'": "" ) ."
					". (isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND classes.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" ) ."
					
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND product_types.id='".$_REQUEST['producttype_id']."'": "" ) ."
					". (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id']) ? " AND products.id='".$_REQUEST['product_id']."'": "" ) ."
						
					ORDER BY classes.class_name, product_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$sessiontypes = db_query("SELECT id, sessiontype_name AS name FROM sessiontypes WHERE !deleted ORDER BY name","Get Session Types");
			
			$sessions = db_query("SELECT id, class_name AS name FROM classes WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND classes.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" )." ORDER BY name","Get Sessions");
			
			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");
			
			$product_types = db_query("SELECT id, product_type AS name FROM product_types WHERE !deleted AND enabled ORDER BY name","Get Product Types");
			
			$products = db_query("SELECT id, CONCAT(product_name,' (',common_name,')') AS name FROM products WHERE !deleted AND !products.exclude AND enabled".(isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" )." ORDER BY name","Get Products");
			
			$page_info['title'] = "Session Products";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?room_id='.$_REQUEST['room_id'].'&date='.$_REQUEST['date'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".
			
			form_select($sessiontypes,array('caption'=>'All Session Types','nocaption'=>'true','name'=>'sessiontype_id','value'=>$_REQUEST['sessiontype_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&date='.$_REQUEST['date'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&class_id='.($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&sessiontype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($sessions,array('caption'=>'All Sessions','nocaption'=>'true','name'=>'class_id','value'=>$_REQUEST['class_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&date='.$_REQUEST['date'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&class_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?room_id=&producttype_id='.$_REQUEST['producttype_id'].'&date='.$_REQUEST['date'].'&product_id='.$_REQUEST['product_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&date='.$_REQUEST['date'].'&product_id='.$_REQUEST['product_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&room_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($product_types,array('caption'=>'All Product Types','nocaption'=>'true','name'=>'producttype_id','value'=>$_REQUEST['producttype_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&date='.$_REQUEST['date'].'&room_id='.$_REQUEST['room_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&product_id='.'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&producttype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($products,array('caption'=>'All Products','nocaption'=>'true','name'=>'product_id','value'=>$_REQUEST['product_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&date='.$_REQUEST['date'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&product_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($dates,array('caption'=>'Select Date','nocaption'=>'true','name'=>'date','value'=>$_REQUEST['date'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&date='.$_REQUEST['date'].'&room_id='.$_REQUEST['room_id'].'&product_id='.$_REQUEST['product_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&date=\'+this.value"','style'=>'width:100px;'));

			if($_SESSION['group_id'] == 1) {
				$page_info['instructions'] .= '&nbsp;&nbsp;&nbsp;<input type="checkbox" name="show_vendor" id="show_vendor"'.(isset($_REQUEST['show_vendor']) && $_REQUEST['show_vendor'] == 1 ? ' checked="checked"':'').' value="1" onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&date='.$_REQUEST['date'].'&product_id='.$_REQUEST['product_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].'&show_vendor='.($_REQUEST['show_vendor'] == 1?'0':'1').'\'" /> <label for="show_vendor">Show Vendor</label><div style="padding-top:10px;">Displays only approved products from orders that are approved. <span style="font-weight:bold;">More data on Export</span></div>';
			}

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Sessions Checked In
		#################################################
		case 'sessions_checked.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Sessions Checked In");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			if(isset($_POST['idReset']) && $_POST['idReset'] != '') { 
				db_query("UPDATE time_slots SET checked_datetime=NULL, checked_user_id=NULL WHERE id='".$_POST['idReset']."'","Reset Checkin");
			}
			if(isset($_POST['scheckoff']) && $_POST['scheckoff'] != '') { 
				db_query("UPDATE time_slots SET checked_datetime='".date('Y-m-d H:i:00')."', checked_user_id='".$_SESSION['user_id']."' WHERE id='".$_POST['scheckoff']."'","Checkin Session");
			}
			
			$tempdates = db_query("SELECT time_slots.start_date
								FROM time_slots
								JOIN classes ON time_slots.class_id=classes.id
								WHERE !classes.deleted AND !time_slots.deleted
								GROUP BY time_slots.start_date
								ORDER BY time_slots.start_date
								","Get dates");
			
			$dates = array();
			while($row = mysqli_fetch_assoc($tempdates)) {
				$dates[$row['start_date']] = pretty_date($row['start_date']);
			}
			
			
			if(!isset($_REQUEST['date']) || !array_key_exists($_REQUEST['date'], $dates)) { 
				$today = date('Y-m-d');
				if(!array_key_exists($today,$dates)) {
					mysqli_data_seek($tempdates,0);
					$temp = mysqli_fetch_assoc($tempdates);
					$_REQUEST['date'] = $temp['start_date'];
				} else {
					$_REQUEST['date'] = $today;
				}
			
			}

			$q = "SELECT time_slots.id, classes.class_name, sessiontypes.sessiontype_name, time_slots.prep_time, time_slots.start_time, rooms.room_name, time_slots.start_date,
					buildings.building_name, time_slots.checked_datetime, CONCAT(users.first_name,' ', users.last_name) as full_name,
					IF(time_slots.checked_datetime != '',1,0) as checked
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN time_slots ON classes.id=time_slots.class_id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					LEFT JOIN users ON time_slots.checked_user_id=users.id
					WHERE !classes.deleted AND !time_slots.deleted AND !rooms.deleted AND !buildings.deleted AND classes.show_id='".$_SESSION['show_id']."' 
					AND time_slots.start_date='".$_REQUEST['date']."'
					ORDER BY checked,time_slots.prep_time, time_slots.start_time, classes.class_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$page_info['title'] = "Session Checkin Report";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?date='.$_REQUEST['date'].'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".form_select($dates,array('caption'=>'Select Date','nocaption'=>'true','name'=>'date','value'=>$_REQUEST['date'],'extra'=>'onchange="location.href=\'?date=\'+this.value"','style'=>'width:100px;'));

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Sessions Report
		#################################################
		case 'sessions.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Sessions Report");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			$tempdates = db_query("SELECT time_slots.start_date
								FROM time_slots
								JOIN classes ON time_slots.class_id=classes.id
								WHERE !classes.deleted AND !time_slots.deleted
								GROUP BY time_slots.start_date
								ORDER BY time_slots.start_date
								","Get dates");
			
			$dates = array();
			while($row = mysqli_fetch_assoc($tempdates)) {
				$dates[$row['start_date']] = pretty_date($row['start_date']);
			}
			
			
			if(!isset($_REQUEST['date']) || !array_key_exists($_REQUEST['date'], $dates)) { 
				$today = date('Y-m-d');
				if(!array_key_exists($today,$dates)) {
					mysqli_data_seek($tempdates,0);
					$temp = mysqli_fetch_assoc($tempdates);
					$_REQUEST['date'] = $temp['start_date'];
				} else {
					$_REQUEST['date'] = $today;
				}
			
			}
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }


			$q = "SELECT time_slots.id, classes.class_name, sessiontypes.sessiontype_name, time_slots.prep_time, 
					time_slots.start_time, time_slots.end_time, time_slots.strike_time, rooms.room_name, 
					buildings.building_name, rooms.description AS room_description, time_slots.description, classes.speaker
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN time_slots ON classes.id=time_slots.class_id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !classes.deleted AND !time_slots.deleted AND !rooms.deleted AND !buildings.deleted AND classes.show_id='".$_SESSION['show_id']."' ". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					AND time_slots.start_date='".$_REQUEST['date']."'
					ORDER BY time_slots.prep_time, time_slots.start_time, classes.class_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }

			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");


			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$page_info['title'] = "Sessions Report";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&date='.$_REQUEST['date'].'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".form_select($dates,array('caption'=>'Select Date','nocaption'=>'true','name'=>'date','value'=>$_REQUEST['date'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&date=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?date='.$_REQUEST['date'].'&room_id=&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?date='.$_REQUEST['date'].'&building_id='.$_REQUEST['building_id'].'&room_id=\'+this.value"','style'=>'width:100px;'));

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;


		#################################################
		##	Product Tracking Report
		#################################################
		case 'tracking.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Product Tracking Report");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
			if(!isset($_REQUEST['producttype_id'])) { $_REQUEST['producttype_id'] = ""; }
			if(!isset($_REQUEST['product_id'])) { $_REQUEST['product_id'] = ""; }
			if(!isset($_REQUEST['tracking_num'])) { $_REQUEST['tracking_num'] = ""; }
			if(!isset($_REQUEST['check_status'])) { $_REQUEST['check_status'] = "1"; }

			$q = "SELECT product_tracking.*, CONCAT(products.product_name,' (',products.common_name,')') AS product_name, product_types.product_type, rooms.room_name, buildings.building_name,
						CONCAT(users.first_name,' ',users.last_name) as user_name,CONCAT(users2.first_name,' ',users2.last_name) as user_name2
					FROM product_tracking
					JOIN products ON product_tracking.product_id=products.id
					JOIN product_types ON products.producttype_id=product_types.id
					JOIN rooms ON product_tracking.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN users ON product_tracking.user_id=users.id
					LEFT JOIN users AS users2 ON product_tracking.check_in_by=users2.id
					WHERE product_tracking.show_id = ".$_SESSION['show_id']." AND products.track_product 
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND product_types.id='".$_REQUEST['producttype_id']."'": "" ) ."
					". (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id']) ? " AND products.id='".$_REQUEST['product_id']."'": "" ) ."
					". (isset($_REQUEST['check_status']) && is_numeric($_REQUEST['check_status']) ? ($_REQUEST['check_status'] == 1 ? " AND product_tracking.check_in IS NULL" : " AND product_tracking.check_in IS NOT NULL") : "" ) ."
					". (isset($_REQUEST['tracking_num']) && $_REQUEST['tracking_num'] != "" ? " AND product_tracking.tracking_number LIKE '%".encode($_REQUEST['tracking_num'])."%'": "" ) ."

					ORDER BY check_out, check_in";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");
			
			$product_types = db_query("SELECT id, product_type AS name FROM product_types WHERE !deleted AND enabled 
										AND id IN (SELECT producttype_id FROM products WHERE !deleted AND track_product)
										ORDER BY name","Get Product Types");
			
			$products = db_query("SELECT id, CONCAT(product_name,' (',common_name,')') AS name FROM products WHERE !deleted AND track_product AND !products.exclude AND enabled".(isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" )." ORDER BY name","Get Products");
			
			$status = array(1=>'Not Checked In',2=>'Checked In');
			
			
			$page_info['title'] = "Product Tracking Report";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&tracking_num='.$_REQUEST['tracking_num'].'&check_status='.$_REQUEST['check_status'].'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".
			
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?room_id=&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&tracking_num='.$_REQUEST['tracking_num'].'&check_status='.$_REQUEST['check_status'].'&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&tracking_num='.$_REQUEST['tracking_num'].'&check_status='.$_REQUEST['check_status'].'&room_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($product_types,array('caption'=>'All Product Types','nocaption'=>'true','name'=>'producttype_id','value'=>$_REQUEST['producttype_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&product_id=&tracking_num='.$_REQUEST['tracking_num'].'&check_status='.$_REQUEST['check_status'].'&producttype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($products,array('caption'=>'All Products','nocaption'=>'true','name'=>'product_id','value'=>$_REQUEST['product_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&tracking_num='.$_REQUEST['tracking_num'].'&check_status='.$_REQUEST['check_status'].'&product_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".

			form_select($status,array('caption'=>'All Check Status','nocaption'=>'true','name'=>'check_status','value'=>$_REQUEST['check_status'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&tracking_num='.$_REQUEST['tracking_num'].'&product_id='.$_REQUEST['product_id'].'&check_status=\'+this.value"','style'=>'width:120px;')).' Tracking #: '.
			
			form_text(array('caption'=>'Tracking Number','nocaption'=>'true','name'=>'tracking_num','value'=>$_REQUEST['tracking_num'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&check_status='.$_REQUEST['check_status'].'&tracking_num=\'+this.value"','style'=>'width:100px;'));


			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Session Products Report
		#################################################
		case 'session_requests.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Session Requests");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['class_id'])) { $_REQUEST['class_id'] = ""; }
			if(!isset($_REQUEST['sessiontype_id'])) { $_REQUEST['sessiontype_id'] = ""; }
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
			if(!isset($_REQUEST['producttype_id'])) { $_REQUEST['producttype_id'] = ""; }
			if(!isset($_REQUEST['product_id'])) { $_REQUEST['product_id'] = ""; }
			if(!isset($_REQUEST['show_vendor']) || $_SESSION['group_id'] != 1 ) { $_REQUEST['show_vendor'] = 0; }

			$q = "SELECT classes.*, CONCAT(session_orders.quote_name,'-',revision) AS jobcode, soi.quantity,	
					CONCAT(products.product_name,' (',common_name,')') as product_name, product_types.product_type, rooms.room_name, 
					rooms.description AS rdescription, buildings.building_name, vendors.vendor_name, time_slots.start_date, time_slots.start_time, 
					time_slots.end_time, sessiontypes.sessiontype_name, if(soi.approved,'Approved','Not Approved') AS status
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN session_orders ON classes.id=session_orders.session_id
					JOIN session_order_items AS soi ON session_orders.id=soi.order_id
					JOIN products ON soi.product_id=products.id
					JOIN product_types ON products.producttype_id=product_types.id
					JOIN time_slots ON soi.timeslot_id=time_slots.id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN vendors ON products.vendor_id=vendors.id
					WHERE !classes.deleted AND !soi.deleted AND !time_slots.deleted 
						AND !rooms.deleted AND !products.deleted AND !products.exclude AND products.enabled AND !product_types.deleted and product_types.enabled
						AND classes.show_id = ".$_SESSION['show_id']."
					". (isset($_REQUEST['class_id']) && is_numeric($_REQUEST['class_id']) ? " AND classes.id='".$_REQUEST['class_id']."'": "" ) ."
					". (isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND classes.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" ) ."
					
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND product_types.id='".$_REQUEST['producttype_id']."'": "" ) ."
					". (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id']) ? " AND products.id='".$_REQUEST['product_id']."'": "" ) ."
						
					ORDER BY classes.class_name, product_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$sessiontypes = db_query("SELECT id, sessiontype_name AS name FROM sessiontypes WHERE !deleted ORDER BY name","Get Session Types");
			
			$sessions = db_query("SELECT id, class_name AS name FROM classes WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND classes.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" )." ORDER BY name","Get Sessions");
			
			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");
			
			$product_types = db_query("SELECT id, product_type AS name FROM product_types WHERE !deleted AND enabled ORDER BY name","Get Product Types");
			
			$products = db_query("SELECT id, CONCAT(product_name,' (',common_name,')') AS name FROM products WHERE !deleted AND !products.exclude AND enabled".(isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" )." ORDER BY name","Get Products");
			
			$page_info['title'] = "Session Requests";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&export=true\'"'));
			$page_info['instructions'] = "Filter: ".
			
			form_select($sessiontypes,array('caption'=>'All Session Types','nocaption'=>'true','name'=>'sessiontype_id','value'=>$_REQUEST['sessiontype_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&class_id='.($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&sessiontype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($sessions,array('caption'=>'All Sessions','nocaption'=>'true','name'=>'class_id','value'=>$_REQUEST['class_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&class_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?room_id=&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&room_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($product_types,array('caption'=>'All Product Types','nocaption'=>'true','name'=>'producttype_id','value'=>$_REQUEST['producttype_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&product_id='.'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&producttype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($products,array('caption'=>'All Products','nocaption'=>'true','name'=>'product_id','value'=>$_REQUEST['product_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].($_SESSION['group_id'] == 1 ? '&show_vendor='.$_REQUEST['show_vendor']:'').'&product_id=\'+this.value"','style'=>'width:100px;'));

			if($_SESSION['group_id'] == 1) {
				$page_info['instructions'] .= '&nbsp;&nbsp;&nbsp;<input type="checkbox" name="show_vendor" id="show_vendor"'.(isset($_REQUEST['show_vendor']) && $_REQUEST['show_vendor'] == 1 ? ' checked="checked"':'').' value="1" onclick="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].'&show_vendor='.($_REQUEST['show_vendor'] == 1?'0':'1').'\'" /> <label for="show_vendor">Show Vendor</label><div style="padding-top:10px;">Displays All requests rather approved or not. <span style="font-weight:bold;">More data on Export</span></div>';
			}

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;


		#################################################
		##	Sessions with no Orders
		#################################################
		case 'no_orders.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Sessions With No Orders");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			$q = "SELECT classes.*, sessiontypes.sessiontype_name, IF(!classes.bill_other,'Main','Other') as billto,
						(SELECT CONCAT(start_date,' ',start_time) as start_dt FROM time_slots WHERE !time_slots.deleted AND class_id=classes.id ORDER BY start_date, prep_time, start_time, end_time, strike_time LIMIT 1) as start_dt, 
						(SELECT COUNT(time_slots.id) FROM time_slots WHERE !time_slots.deleted AND class_id=classes.id) AS time_count
					FROM classes
					JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
					LEFT JOIN session_orders AS so ON so.session_id=classes.id
					LEFT JOIN order_status as status ON status.id=so.status_id
					WHERE !classes.deleted AND classes.show_id='".$_SESSION['show_id']."' AND so.id IS NULL
					ORDER BY start_dt, classes.class_name";
				
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$page_info['title'] = "Sessions With No Orders";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?export=true\'"'));
			$page_info['instructions'] = 'This report shows all sessions with NO orders submitted';
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;


		#################################################
		##	Room/Session Products
		#################################################
		case 'room_session_products.php':
			# Adding in the lib file
			
			if(isset($_POST['listids']) && count($_POST['listids'])) { $NON_HTML_PAGE = true; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Room/Session Products Report");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			$tempdates = db_query("SELECT time_slots.start_date
								FROM time_slots
								JOIN classes ON time_slots.class_id=classes.id
								WHERE !classes.deleted AND !time_slots.deleted
								GROUP BY time_slots.start_date
								ORDER BY time_slots.start_date
								","Get dates");
			
			$dates = array();
			while($row = mysqli_fetch_assoc($tempdates)) {
				$dates[$row['start_date']] = pretty_date($row['start_date']);
			}
			
			
			if(!isset($_REQUEST['date']) || !array_key_exists($_REQUEST['date'], $dates)) { 
				$today = date('Y-m-d');
				if(!array_key_exists($today,$dates)) {
					mysqli_data_seek($tempdates,0);
					$temp = mysqli_fetch_assoc($tempdates);
					$_REQUEST['date'] = $temp['start_date'];
				} else {
					$_REQUEST['date'] = $today;
				}
			
			}


			$q = "SELECT rooms.id, rooms.room_name, rooms.description, buildings.building_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN time_slots AS ts ON ts.room_id=rooms.id
					WHERE !rooms.deleted AND !ts.deleted AND ts.start_date = '".$_REQUEST['date']."' AND !buildings.deleted
					GROUP BY rooms.id
					ORDER BY room_name

			";
			
/*
			$q = "SELECT time_slots.id, classes.class_name, sessiontypes.sessiontype_name, time_slots.prep_time, time_slots.start_time, rooms.room_name, time_slots.start_date,
					buildings.building_name, time_slots.checked_datetime, CONCAT(users.first_name,' ', users.last_name) as full_name
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN time_slots ON classes.id=time_slots.class_id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					LEFT JOIN users ON time_slots.checked_user_id=users.id
					WHERE !classes.deleted AND !time_slots.deleted AND !rooms.deleted AND !buildings.deleted AND classes.show_id='".$_SESSION['show_id']."' 
					AND time_slots.start_date='".$_REQUEST['date']."'
					ORDER BY time_slots.prep_time, time_slots.start_time, classes.class_name";
*/				
			$results = db_query($q,"getting report data");
			
			if(isset($_POST['listids']) && count($_POST['listids'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
?>		<script type='text/javascript'>
			function submit_form() {
				document.getElementById('idForm').submit();
			}
		</script>		
<?				
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$page_info['title'] = "Room/Session Products Report";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Print Report','extra'=>'onclick="submit_form();"'));
			$page_info['instructions'] = "<form action='' method='post' id='idForm' style='padding:0;margin:0;'>
Filter: ".form_select($dates,array('caption'=>'Select Date','nocaption'=>'true','name'=>'date','value'=>$_REQUEST['date'],'extra'=>'onchange="location.href=\'?date=\'+this.value"','style'=>'width:100px;'));

			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;


		#################################################
		##	View Page
		#################################################
		case 'view.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Room/Session Products Report");
			$breadcrumbs[] = array('TEXT' => date('n/j/Y',strtotime($_REQUEST['date']))." - View Room");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['date']) || $_REQUEST['date'] == "") { errorPage('Invalid Selection'); } // Check Required Field for Query
			if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) { errorPage('Invalid Selection'); } // Check Required Field for Query

			$q = "SELECT rooms.*, buildings.building_name
				FROM rooms
				JOIN buildings ON rooms.building_id=buildings.id
				JOIN time_slots AS ts ON ts.room_id=rooms.id
				WHERE !rooms.deleted AND !ts.deleted AND ts.start_date = '".$_REQUEST['date']."' AND !buildings.deleted 
					AND rooms.id = '".$_REQUEST['id']."'
				GROUP BY rooms.id
				ORDER BY room_name";

			$info = db_query($q,"getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid Selection'); } // Did we get a result?
			
			if(isset($_POST['room_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "View: ".$info['room_name']." ".$info['description']." - ".date('n/j/Y',strtotime($_REQUEST['date']));
			$page_info['instructions'] = '<a href="room_session_products.php?date='.$_REQUEST['date'].'">Back to List</a>';

			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Session Products Report
		#################################################
		case 'session_requests2.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Session Requests Single Lines");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			if(!isset($_REQUEST['class_id'])) { $_REQUEST['class_id'] = ""; }
			if(!isset($_REQUEST['sessiontype_id'])) { $_REQUEST['sessiontype_id'] = ""; }
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
			if(!isset($_REQUEST['status_id'])) { $_REQUEST['status_id'] = ""; }

			//First we need to get the session Product Types
			$q = "SELECT product_types.id, product_types.product_type
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN session_orders ON classes.id=session_orders.session_id
					JOIN session_order_items AS soi ON session_orders.id=soi.order_id
					JOIN products ON soi.product_id=products.id
					JOIN product_types ON products.producttype_id=product_types.id
					JOIN time_slots ON soi.timeslot_id=time_slots.id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN vendors ON products.vendor_id=vendors.id
					WHERE !classes.deleted AND !soi.deleted AND !time_slots.deleted 
						AND !rooms.deleted AND !products.deleted AND !products.exclude AND products.enabled AND !product_types.deleted and product_types.enabled
						AND classes.show_id = ".$_SESSION['show_id']."
					". (isset($_REQUEST['class_id']) && is_numeric($_REQUEST['class_id']) ? " AND classes.id='".$_REQUEST['class_id']."'": "" ) ."
					". (isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND classes.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" ) ."
					
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['status_id']) && is_numeric($_REQUEST['status_id']) ? " AND session_orders.status_id='".$_REQUEST['status_id']."'": "" ) ."
					GROUP BY product_types.id	
					ORDER BY id, product_type";
					
			$pt = db_query($q,"Getting Product Types");


			$q = "SELECT ts.*, c.class_name, c.speaker, c.bill_other, st.sessiontype_name, r.room_number, b.building_name,
					CONCAT(so.quote_name,'-',so.revision) AS jobcode, r.description AS rdescription, os.order_status";

			while($pt_row = mysqli_fetch_assoc($pt)) {
				$q .= ", (SELECT GROUP_CONCAT(CONCAT('(',soi.quantity,') ',p.product_name) ORDER BY p.product_name DESC SEPARATOR ', ')
						FROM session_order_items AS soi
						JOIN products AS p ON soi.product_id=p.id
						WHERE !soi.deleted AND !p.deleted AND !p.exclude AND p.enabled AND soi.timeslot_id=ts.id AND p.producttype_id='".$pt_row['id']."'
						) AS products_".$pt_row['id'].", (SELECT sn.note
							FROM session_notes AS sn 
							WHERE sn.timeslot_id=ts.id AND sn.producttype_id='".$pt_row['id']."' AND note_type_id=1
						) AS custom_".$pt_row['id'].",

						(SELECT GROUP_CONCAT(CONCAT(pt_questions.question,': ',if(answers.answer != '',answers.answer,'N/A')) ORDER BY pt_questions.sort_order ASC 
							SEPARATOR ' -- ')
							FROM answers
							JOIN pt_questions ON answers.question_id=pt_questions.id
							WHERE answers.timeslot_id=ts.id AND !pt_questions.deleted AND pt_questions.producttype_id='".$pt_row['id']."'
							) AS questions_".$pt_row['id']."
						";
			}			
			
			$q .= ", (SELECT note FROM session_notes WHERE note_type_id='3' AND timeslot_id=ts.id) AS presenter_bringing, (SELECT note FROM session_notes WHERE note_type_id='2' AND order_id=so.id) AS Order_Notes
					FROM time_slots AS ts
					JOIN classes AS c ON ts.class_id=c.id
					JOIN rooms AS r ON ts.room_id=r.id
					JOIN buildings AS b ON r.building_id=b.id
					JOIN sessiontypes AS st ON c.sessiontype_id=st.id
					JOIN session_orders AS so ON c.id=so.session_id
					JOIN order_status AS os ON so.status_id=os.id
					WHERE !ts.deleted AND !c.deleted AND !r.deleted AND !b.deleted AND c.show_id = '".$_SESSION['show_id']."' AND so.quote_name IS NOT NULL
					". (isset($_REQUEST['class_id']) && is_numeric($_REQUEST['class_id']) ? " AND c.id='".$_REQUEST['class_id']."'": "" ) ."
					". (isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND c.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" ) ."
					
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND r.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND r.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['status_id']) && is_numeric($_REQUEST['status_id']) ? " AND so.status_id='".$_REQUEST['status_id']."'": "" ) ."
					ORDER BY ts.start_date, ts.start_time, ts.end_time, c.class_name";

			
			$results = db_query($q,"getting report data");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "report";
			}

			$sessiontypes = db_query("SELECT id, sessiontype_name AS name FROM sessiontypes WHERE !deleted ORDER BY name","Get Session Types");
			
			$sessions = db_query("SELECT id, class_name AS name FROM classes WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['sessiontype_id']) && is_numeric($_REQUEST['sessiontype_id']) ? " AND classes.sessiontype_id='".$_REQUEST['sessiontype_id']."'": "" )." ORDER BY name","Get Sessions");
			
			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");
			
			$status = db_query("SELECT id, order_status AS name FROM order_status ","Get Status");

			$page_info['title'] = "Session Requests Single Lines";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?room_id='.$_REQUEST['room_id'].'&building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].'&status_id='.$_REQUEST['status_id'].'&export=true\'"'));
			$page_info['instructions'] = "<p>Export report to see products..</p>Filter: ".
			
			form_select($sessiontypes,array('caption'=>'All Session Types','nocaption'=>'true','name'=>'sessiontype_id','value'=>$_REQUEST['sessiontype_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&building_id='.$_REQUEST['building_id'].'&status_id='.$_REQUEST['status_id'].'&class_id='.'&sessiontype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($sessions,array('caption'=>'All Sessions','nocaption'=>'true','name'=>'class_id','value'=>$_REQUEST['class_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&building_id='.$_REQUEST['building_id'].'&status_id='.$_REQUEST['status_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?room_id=&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].'&status_id='.$_REQUEST['status_id'].'&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&status_id='.$_REQUEST['status_id'].'&class_id='.$_REQUEST['class_id'].'&room_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($status,array('caption'=>'All Status','nocaption'=>'true','name'=>'status_id','value'=>$_REQUEST['status_id'],'extra'=>'onchange="location.href=\'?room_id='.$_REQUEST['room_id'].'&building_id='.$_REQUEST['building_id'].'&sessiontype_id='.$_REQUEST['sessiontype_id'].'&class_id='.$_REQUEST['class_id'].'&status_id=\'+this.value"','style'=>'width:100px;'));;

			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;


		#################################################
		##	Else show Error Page
		#################################################
		default:
			include($BF .'_lib.php');
			errorPage('Page Incomplete.  Please notify an Administrator that you have received this error.');
	}

?>