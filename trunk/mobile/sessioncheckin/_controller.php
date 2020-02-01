<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm');
			include_once($BF.'components/formfields.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
			}

			# The template to use (should be the last thing before the break)
			$section = '';
			include($BF ."models/mobile.php");		
			
			break;
		#################################################
		##	Sessions List Page
		#################################################
		case 'sessions.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm');
			include_once($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['date']) || $_REQUEST['date'] == "") { errorPage('Invalid Date'); } // Check Required Field for Query


			$q = "SELECT time_slots.id, classes.class_name, time_slots.prep_time, 
					time_slots.start_time, time_slots.end_time, time_slots.strike_time, rooms.room_name, 
					buildings.building_name, time_slots.start_date, time_slots.checked_datetime
					FROM classes
					JOIN sessiontypes ON classes.sessiontype_id=sessiontypes.id
					JOIN time_slots ON classes.id=time_slots.class_id
					JOIN rooms ON time_slots.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !classes.deleted AND !time_slots.deleted AND !rooms.deleted AND !buildings.deleted AND classes.show_id='".$_SESSION['show_id']."' ". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					AND time_slots.start_date='".$_REQUEST['date']."'
					ORDER BY checked_datetime, time_slots.prep_time, time_slots.start_time, classes.class_name";
				
			$results = db_query($q,"getting report data");


			# Stuff In The Header
			function sith() { 
				global $BF,$results;
?>
	<script type='text/javascript'>
		var sessions = new Array();
		var ids = new Array();
		var i = 0;
<?
	while($row = mysqli_fetch_assoc($results)) {
?>
		sessions[i] = '<?=$row['class_name'].$row['room_name']?>';
		ids[i] = '<?=$row['id']?>';
		i++;
<?		
	}
?>		

		function look_for(value) {
			for (i=0; i<sessions.length; i++) {
				var reg = new RegExp(value,'i');
				if (reg.test(sessions[i])) {
					document.getElementById('session_'+ids[i]).style.display='';
				} else {
					document.getElementById('session_'+ids[i]).style.display='none';
				}
			}
		}
		
	</script>
<?
				mysqli_data_seek($results,0);
			}

			# The template to use (should be the last thing before the break)
			$section = '';
			include($BF ."models/mobile.php");		
			
			break;

		#################################################
		##	Session Page
		#################################################
		case 'session.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm');
			include_once($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) { errorPage('Invalid Session'); } // Check Required Field for Query

			$info = db_query("SELECT ts.*, 
								c.audience_size, c.class_name, c.speaker, c.description AS cdescription, c.notes AS cnotes, c.bill_other, r.internet_access, r.room_name, r.capacity, r.square_feet, r.description AS rdescription, r.checked_datetime AS rchecked_datetime, b.building_name, c.bill_name, u1.first_name AS rfirst_name, u1.last_name AS rlast_name, u1.cellnumber AS rcellnumber, mc1.text_method AS rtext_method, u2.first_name AS sfirst_name, u2.last_name AS slast_name, u2.cellnumber AS scellnumber, mc2.text_method AS stext_method 
								FROM time_slots AS ts
								JOIN classes AS c ON ts.class_id=c.id
								JOIN rooms AS r ON ts.room_id=r.id
								JOIN buildings AS b ON r.building_id=b.id
								LEFT JOIN users AS u1 ON r.checked_user_id=u1.id
								LEFT JOIN mobile_carriers AS mc1 ON u1.mobile_carrier_id=mc1.id
								LEFT JOIN users AS u2 ON ts.checked_user_id=u2.id
								LEFT JOIN mobile_carriers AS mc2 ON u2.mobile_carrier_id=mc2.id

								WHERE !ts.deleted AND !c.deleted AND c.show_id='".$_SESSION['show_id']."' AND !r.deleted
									AND ts.id='".$_REQUEST['id']."'
								
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid Session'); } // Did we get a result?

			if(isset($_POST['check-off'])) {
				db_query("UPDATE time_slots SET checked_datetime='".date('Y-m-d H:i:00')."', checked_user_id='".$_SESSION['user_id']."' WHERE id='".$_REQUEST['id']."'",'Checking Off');
				header("Location: session.php?id=". $_REQUEST['id']);
				die();
			}

			# Stuff In The Header
			function sith() { 
				global $BF;
?>		<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
		<script type='text/javascript'>
			function show_more() {
				var view_more = document.getElementById('more_data').style;
				var view_btn = document.getElementById('show_btn');
				if(view_more.display == 'none') {
					view_more.display = '';
					view_btn.value = 'Hide Details';
				} else {
					view_more.display = 'none';
					view_btn.value = 'Show More Details';
				}
			}
			function show_rproducts() {
				var rp = document.getElementById('room_products');
				var btn = document.getElementById('btn_room_products');
				if(rp.style.display == 'none') {
					rp.style.display = '';
					btn.value = 'Hide Room Products';
					
				} else {
					rp.style.display = 'none';
					btn.value = 'Show Room Products';
				}
			
			}
		</script>
<?
			}

			# The template to use (should be the last thing before the break)
			$section = '';
			include($BF ."models/mobile.php");		
			
			break;


		#################################################
		##	Else show Error Page
		#################################################
		default:
			include($BF .'mobile/_lib.php');
			errorPage('Page Incomplete.  Please notify an Administrator that you have received this error.');
	}

?>