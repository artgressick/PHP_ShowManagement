<?
	# 12/21/2009 - Wes Grimes - Added sub select to pull in speaker name - Line 32
	# 12/21/2009 - Wes Grimes - Added sub select to pull in speaker name - Line 78	
	# 12/21/2009 - Wes Grimes - Added sub select to pull in financial info - Line 80-82
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Session";
	$breadcrumbs[] = array('URL' => $BF.'sessions/', 'TEXT' => "Sessions");
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section." List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');

			$q = "SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name, IF(!classes.bill_other,'Main','Other') as billto,
						(SELECT CONCAT(start_date,' ',start_time) as start_dt FROM time_slots WHERE !time_slots.deleted AND class_id=classes.id ORDER BY start_date, prep_time, start_time, end_time, strike_time LIMIT 1) as start_dt, 
						(SELECT COUNT(time_slots.id) FROM time_slots WHERE !time_slots.deleted AND class_id=classes.id) AS time_count,
						(SELECT rooms.room_name 
						FROM time_slots 
						JOIN rooms ON time_slots.room_id=rooms.id
						WHERE !time_slots.deleted AND class_id=classes.id 
						ORDER BY start_date, prep_time, start_time, end_time, strike_time LIMIT 1) as room_name,
						(SELECT CONCAT(first_name, ' ',last_name) FROM speakers WHERE speakers.id = classes.speaker_id) as speaker
					FROM classes
					JOIN sessioncats on classes.sessioncat_id = sessioncats.id
					JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
					WHERE !classes.deleted AND classes.show_id='".$_SESSION['show_id']."'
					ORDER BY start_dt, classes.class_name";
				
			$results = db_query($q,"getting classes");
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "classes";
			}

			$page_info['title'] = $section." List";
			$page_info['instructions'] = "Click a Session to view.";
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	View Page
		#################################################
		case 'view.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "View ".$section);
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT classes.*,
								(SELECT CONCAT(first_name, ' ',last_name) FROM speakers WHERE speakers.id = classes.speaker_id) as speaker,
								(SELECT email FROM speakers WHERE speakers.id = classes.speaker_id) as speaker_email,
								(if financial_id is not null then financial_id else -1000 endif) as financial_responsibility,
								(SELECT CONCAT(first_name, ' ',last_name) FROM speakers WHERE speakers.id = classes.financial_id) as fin_name,
								(SELECT email FROM speakers WHERE speakers.id = classes.financial_id) as fin_email 
								FROM classes 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			$show_data = db_query("SELECT * FROM shows WHERE id=".$_SESSION['show_id'],"Getting Show Information",1);
			
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
			
			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Day View Page
		#################################################
		case 'dayview.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section." Day View");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			//Get Dates of the Show
			$showinfo = db_query("SELECT * FROM shows WHERE id=".$_SESSION['show_id'],"Get Show Data",1);
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				
?>		<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
		<script type='text/javascript'>
			document.onmousemove = mouseMove;
			function mouseMove(ev){ 
				ev           = ev || window.event; 
				var mousePos = mouseCoords(ev); 
			} 
			 
			function mouseCoords(ev){ 
				if(ev.pageX || ev.pageY){ 
					var mouseX = ev.pageX;
					var mouseY = ev.pageY;
				} else {
					var mouseX = ev.clientX + document.body.scrollLeft - document.body.clientLeft;
					var mouseY = ev.clientY + document.body.scrollTop;
				}
				var innerbodytx = findPosX(document.getElementById('innerbody'));
				var innerbodybx = innerbodytx + document.getElementById('innerbody').offsetWidth;
				var innerbodyty = findPosY(document.getElementById('innerbody'));
				var innerbodyby = innerbodyty + document.getElementById('innerbody').offsetHeight;
				if(mouseX <= innerbodytx || mouseX >= innerbodybx || mouseY <= innerbodyty || mouseY >= innerbodyby) {
					hideinfoBox();
				}

				
				// This specifically finds the height of the entire internal window (the page) that you are currently in.
				if( typeof( window.innerWidth ) == 'number' ) {
					//Non-IE
					myWidth = window.innerWidth;
					myHeight = window.innerHeight;
				} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
					//IE 6+ in 'standards compliant mode'
					myWidth = document.documentElement.clientWidth;
					myHeight = document.documentElement.clientHeight;
				} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
					//IE 4 compatible
					myWidth = document.body.clientWidth;
					myHeight = document.body.clientHeight;
				}				

				document.getElementById("posY").value = mouseY;
				document.getElementById("posX").value = mouseX;
				
				if(parseInt(mouseX) + parseInt(document.getElementById('infobox').offsetWidth) + parseInt(25) > parseInt(myWidth)) {
					var boxX = parseInt(mouseX) - parseInt(document.getElementById('infobox').offsetWidth) - parseInt(10);
				} else {
					var boxX = parseInt(mouseX) + parseInt(10)
				}

				if(parseInt(mouseY) + parseInt(document.getElementById('infobox').offsetHeight) > parseInt(myHeight)) {
					var boxY = parseInt(mouseY) - parseInt(document.getElementById('infobox').offsetHeight);
				} else {
					var boxY = parseInt(mouseY)
				}

				document.getElementById("infobox").style.top = (boxY)+"px";
				document.getElementById("infobox").style.left = (boxX)+"px";
			} 

			function showinfoBox(id) {
				document.getElementById("infobox").style.display = "";
				getSessionInfo("<?=$BF?>",id);
			}
			
			function session_data_ready() {
				document.getElementById("infoloading").style.display = "none";
				document.getElementById("infodata").style.display = "";


				var mouseX = document.getElementById("posX").value;
				var mouseY = document.getElementById("posY").value;

				// This specifically finds the height of the entire internal window (the page) that you are currently in.
				if( typeof( window.innerWidth ) == 'number' ) {
					//Non-IE
					myWidth = window.innerWidth;
					myHeight = window.innerHeight;
				} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
					//IE 6+ in 'standards compliant mode'
					myWidth = document.documentElement.clientWidth;
					myHeight = document.documentElement.clientHeight;
				} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
					//IE 4 compatible
					myWidth = document.body.clientWidth;
					myHeight = document.body.clientHeight;
				}				
				
				
				if(parseInt(mouseX) + parseInt(document.getElementById('infobox').offsetWidth) + parseInt(25) > parseInt(myWidth)) {
					var boxX = parseInt(mouseX) - parseInt(document.getElementById('infobox').offsetWidth) - parseInt(10);
				} else {
					var boxX = parseInt(mouseX) + parseInt(10)
				}

				if(parseInt(mouseY) + parseInt(document.getElementById('infobox').offsetHeight) > parseInt(myHeight)) {
					var boxY = parseInt(mouseY) - parseInt(document.getElementById('infobox').offsetHeight);
				} else {
					var boxY = parseInt(mouseY)
				}

				document.getElementById("infobox").style.top = (boxY)+"px";
				document.getElementById("infobox").style.left = (boxX)+"px";

			}
			
			function hideinfoBox() {
				document.getElementById("infobox").style.display = "none";
				document.getElementById("infoloading").style.display = "";
				document.getElementById("infodata").style.display = "none";
			}
			
			function findPosX(obj) {
				var curleft = 0;
				if(obj.offsetParent) {
					while(1) {
						curleft += obj.offsetLeft;
						if(!obj.offsetParent)
							break;
						obj = obj.offsetParent;
					}
				} else if(obj.x) {
					curleft += obj.x;
				}
				return curleft;
			}
			
			function findPosY(obj) {
				var curtop = 0;
				if(obj.offsetParent) {
					while(1) {
						curtop += obj.offsetTop;
						if(!obj.offsetParent)
							break;
						obj = obj.offsetParent;
					}
				} else if(obj.y) {
					curtop += obj.y;
				}
				return curtop;
				
			}

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
		##	Order Page
		#################################################
		case 'order.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			//If the person is passed from ISTE's website we need to do some work first
			if(isset($_REQUEST['d']) && $_REQUEST['d'] != "") {
				parse_str(base64_decode($_REQUEST['d']), $temp_data);
				if(isset($temp_data['key']) && $temp_data['key'] = date('Ymd')) {
				
					if(isset($temp_data['session_id']) && is_numeric($temp_data['session_id'])) {
						$test = db_query("SELECT * FROM classes WHERE !deleted AND id='".$temp_data['session_id']."'","Get Info",1);
						if($test['id'] != "") {
							
							$row = db_query("SELECT users.*, groups.admin_access, groups.orders_quoted, groups.group_name FROM users JOIN groups ON users.group_id=groups.id WHERE users.id=1","Get User Info",1);
						
							$_SESSION['user_email'] = $row["email"];
							$_SESSION['user_id'] = $row["id"];
							$_SESSION['first_name'] = $row["first_name"];
							$_SESSION['last_name'] = $row["last_name"];
							$_SESSION['group_id'] = $row['group_id'];
							$_SESSION['admin_access'] = $row['admin_access'];
							$_SESSION['orders_quoted'] = $row['orders_quoted'];
							$_SESSION['group_name'] = $row['group_name'];
							$_SESSION['logedin_at'] = date('m/d/Y H:m:s');
							$_SESSION['lastsecuritycheck_at'] = date('m/d/Y H:i:s');
							
							$show = db_query("SELECT id, show_name FROM shows WHERE !deleted AND id=".$test['show_id'],"getting shows",1);
							$_SESSION['show_name'] = $show['show_name'];
							$_SESSION['show_id'] = $show['id'];
							
							$_SESSION['auto_logged'] = true;
							
							header("Location: order.php?key=".$test['lkey']);
							die();
						} else {
							errorPage('Invalid Session');
						}
					} else {
						errorPage('Invalid Session');
					}
				} else {
					errorPage('Invalid Security Key');
				}
			
			}

			$breadcrumbs[] = array('TEXT' => "Session Products");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name, sessioncats.ignorehiddenroom
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

			if($info['imported']) { 
				include_once("_import_session.php");
				$info = db_query("
									SELECT classes.*, sessiontypes.sessiontype_name, sessioncats.sessioncat_name, sessioncats.ignorehiddenroom
									FROM classes
									JOIN sessioncats on classes.sessioncat_id = sessioncats.id
									JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
									WHERE classes.lkey='". $_REQUEST['key'] ."' AND !classes.deleted
				","getting info",1); // Get Info
			}

			if($info['bill_other']) {
				$bodyParams = "calculate_total_bill();";
			}

			
			# Stuff In The Header
			function sith() { 
				global $BF,$info;
				include($BF .'components/list/sortlistjs.php');
?>
	<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
	<script type="text/javascript" src='<?=$BF?>includes/forms.js'></script>				
	<script type='text/javascript'>
		function check_product(id) {
			if(document.getElementById('product_'+id).checked==true) {
				document.getElementById('qty_'+id).value = document.getElementById('orig_qty_'+id).value;
				if(document.getElementById('qty_'+id).type == 'text') {
					document.getElementById('qty_'+id).disabled=false;
					document.getElementById('qty_'+id).focus();
				}
			} else {
				document.getElementById('qty_'+id).value='';
				if(document.getElementById('qty_'+id).type == 'text') {
					document.getElementById('qty_'+id).disabled=true;
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
		
		function show_more_products(key,pt) {
			window.open('view_more.php?key='+key+'&pt='+pt+'',null,'location=no, status=1, height=700, width=600, resizable=yes, scrollbars=yes, toolbar=no, menubar=no');
		
		}
		
//		linkto(array('address'=>'#','display'=>"(View ALL)",'extra'=>'onclick="window.open(\'view_more.php?key='.$roominfo['lkey'].'&pt='.$rp['id'].'\', \'View Products\', \'location=no, status=1, height=700, width=600, resizable=yes, scrollbars=yes, toolbar=no, menubar=no\')"','style'=>'color:red;'))
		
		function custom_check(id) {
			var c_old = document.getElementById('orig_custom_'+id).value;
			var c_new = document.getElementById('custom_'+id).value;
			var status_id = document.getElementById('status_id').value;
			if(c_old != c_new && status_id == 2) {
				document.getElementById('orig_status').style.display='none';
				document.getElementById('pend_status').style.display='';
			}			
		}
		function calculate_total_bill() {
			var grand_total = 0;
			var product_total = 0;
			var setup_total = 0;
			var cont = (parseInt(document.getElementById('contingency').innerHTML) / 100);
			var cont_total = 0;
			
			var all_ids = document.getElementById('product_ids').value;
			if(all_ids != "") {
				var ids = all_ids.split(","); 
				for ( var i in ids ) {
					if(document.getElementById('product_'+ids[i])) {
						if(document.getElementById('product_'+ids[i]).checked == true || document.getElementById('product_'+ids[i]).value == 'on') {
							if(!IsWhole(document.getElementById('qty_' + ids[i]).value)) { document.getElementById('qty_' + ids[i]).value = 1; }
							var tprice = (+document.getElementById('price_' + ids[i]).value) * parseInt(document.getElementById('qty_' + ids[i]).value);
							var tsetup = (+document.getElementById('setup_' + ids[i]).value) * parseInt(document.getElementById('qty_' + ids[i]).value);
							var ttotal = tprice + tsetup;
							product_total = product_total + tprice;
							setup_total = setup_total + tsetup;
							if(document.getElementById('orig_qty_'+ids[i])) {
								var qold = document.getElementById('orig_qty_'+ids[i]).value;
								var qnew = document.getElementById('qty_'+ids[i]).value;
	
								if(qold != qnew && document.getElementById('app_'+ids[i]).style.display=='') {
									document.getElementById('app_'+ids[i]).style.display='none';
									document.getElementById('notapp_'+ids[i]).style.display='';
									var status_id = document.getElementById('status_id').value;
									if(status_id == 2) {
										document.getElementById('orig_status').style.display='none';
										document.getElementById('pend_status').style.display='';
									}
									document.getElementById('total_disclaimer').style.display='';
								}
							}
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
		
		
		
		var totalErrors = 0;
		function error_check() {
			if(document.getElementById('btnpress').value == 1) { 
				if(totalErrors != 0) { reset_errors(); }  
				
				totalErrors = 0;
<?					
				$datestimes = db_query('SELECT time_slots.id 
										FROM time_slots 
										JOIN rooms ON time_slots.room_id=rooms.id
										JOIN buildings ON rooms.building_id = buildings.id
										WHERE !time_slots.deleted AND class_id='.$info['id'].' 
										ORDER BY time_slots.start_date, time_slots.start_time, time_slots.end_time, 
										time_slots.description','Getting Dates and Times');
										
				$tmp_question = db_query("SELECT pt_questions.*
										FROM pt_questions 
										WHERE !pt_questions.deleted AND pt_questions.show_id='".$_SESSION['show_id']."' 
										ORDER BY pt_questions.sort_order, pt_questions.question
										",'Getting Question');
while($dt_row = mysqli_fetch_assoc($datestimes))	{									
	$i = 1;
	while($qu_row = mysqli_fetch_assoc($tmp_question)) {
		if($qu_row['required'] && $qu_row['fieldtype_id'] != 1) {
			if(in_array($qu_row['fieldtype_id'],array(2,3,4,5))) {
?>
			if(document.getElementById('answer_<?=$dt_row['id']?>_<?=$qu_row['id']?>')) {
			 	if(errEmpty('answer_<?=$dt_row['id']?>_<?=$qu_row['id']?>', "You must enter an answer for \"<?=$qu_row['question']?>\"")) { totalErrors++; }
			} 	
<?
			} else {
?> 
			if(document.getElementById('answer_<?=$dt_row['id']?>_<?=$qu_row['id']?>-0')) {
				if(errEmpty('answer_<?=$dt_row['id']?>_<?=$qu_row['id']?>[]', "You must select an answer for \"<?=$qu_row['question']?>\"","array")) { totalErrors++; }
			}
<?
			}
		}
		$i++;
	}
}
?>
				if(totalErrors == 0) {
					document.getElementById('idForm').submit();
				} else {	
					window.scrollTo(0,0);
					return false;
				}
			} else { return false; }
		}
	</script>
<?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF,$info;
			}

			# The template to use (should be the last thing before the break)
			
			include($BF ."models/template.php");		
			
			break;
		#################################################
		##	View More Page
		#################################################
		case 'view_more.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			$info = db_query("SELECT * FROM product_types WHERE id='".$_REQUEST['pt']."'","Get Product Type",1);

			$q = "SELECT products.id, CONCAT(IF(room_products.quantity>1,CONCAT('(',room_products.quantity,' x) '),''),products.product_name) AS products
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
		##	Index Page
		#################################################
		case 'rooms.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Room List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');

			$q = "SELECT rooms.*, buildings.building_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !rooms.deleted AND rooms.show_id=".$_SESSION['show_id']."
					ORDER BY rooms.room_number";
				
			$results = db_query($q,"getting rooms");
			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "rooms";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = "Room List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a Room to View";
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;
		#################################################
		##	Edit Page
		#################################################
		case 'viewroom.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('URL' => $BF.'sessions/rooms.php', 'TEXT' => "Room List");
			$breadcrumbs[] = array('TEXT' => "View Room");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','1');
			include_once($BF.'components/formfields.php');
			
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM rooms 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['room_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			# The template to use (should be the last thing before the break)
			
//			$page_info['title'] = "View ".$section.": ".$info['room_name'];
//			$page_info['instructions'] = '';

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