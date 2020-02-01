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

			$buildings = db_query("SELECT id, lkey, building_name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY building_name","Getting Buildings");

			# Stuff In The Header
			function sith() { 
				global $BF,$buildings;
?>
	<script type='text/javascript'>
		var buildings = new Array();
		var ids = new Array();
		var i = 0;
<?
	while($row = mysqli_fetch_assoc($buildings)) {
?>
		buildings[i] = '<?=$row['building_name']?>';
		ids[i] = '<?=$row['id']?>';
		i++;
<?		
	}
?>		

		function look_for(value) {
			for (i=0; i<buildings.length; i++) {

				var reg = new RegExp(value,'i');
				if (reg.test(buildings[i])) {
					document.getElementById('building_'+ids[i]).style.display='';
				} else {
					document.getElementById('building_'+ids[i]).style.display='none';
				}
			}

		}
		
	</script>
<?
				mysqli_data_seek($buildings,0);
			}

			# The template to use (should be the last thing before the break)
			$section = '';
			include($BF ."models/mobile.php");		
			
			break;
		#################################################
		##	Rooms List Page
		#################################################
		case 'rooms.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm');
			include_once($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid Building'); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM buildings 
								WHERE lkey='". $_REQUEST['key'] ."' AND !deleted
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid Building'); } // Did we get a result?

			$rooms = db_query("SELECT r.id, r.lkey, r.description, r.room_name, r.building_id
						   FROM rooms AS r
						   WHERE !r.deleted AND r.show_id=".$_SESSION['show_id']." AND r.building_id='".$info['id']."'
						   ORDER BY room_name, description","Getting Rooms");



			# Stuff In The Header
			function sith() { 
				global $BF,$rooms;
?>
	<script type='text/javascript'>
		var rooms = new Array();
		var ids = new Array();
		var i = 0;
<?
	while($row = mysqli_fetch_assoc($rooms)) {
?>
		rooms[i] = '<?=$row['room_name'].$row['description']?>';
		ids[i] = '<?=$row['id']?>';
		i++;
<?		
	}
?>		

		function look_for(value) {
			for (i=0; i<rooms.length; i++) {

				var reg = new RegExp(value,'i');
				if (reg.test(rooms[i])) {
					document.getElementById('room_'+ids[i]).style.display='';
				} else {
					document.getElementById('room_'+ids[i]).style.display='none';
				}
			}

		}
		
	</script>
<?
				mysqli_data_seek($rooms,0);

			}

			# The template to use (should be the last thing before the break)
			$section = '';
			include($BF ."models/mobile.php");		
			
			break;

		#################################################
		##	Rooms List Page
		#################################################
		case 'room.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm');
			include_once($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid Room'); } // Check Required Field for Query

			$info = db_query("
								SELECT rooms.*, users.first_name, users.last_name, users.cellnumber, mobile_carriers.text_method,
									buildings.building_name
								FROM rooms
								JOIN buildings ON rooms.building_id=buildings.id
								LEFT JOIN users ON rooms.checked_user_id=users.id
								LEFT JOIN mobile_carriers ON users.mobile_carrier_id=mobile_carriers.id
								WHERE rooms.lkey='". $_REQUEST['key'] ."' AND !rooms.deleted
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid Room'); } // Did we get a result?

			if(isset($_POST['check-off'])) {
				db_query("UPDATE rooms SET checked_datetime='".date('Y-m-d H:i:00')."', checked_user_id='".$_SESSION['user_id']."' WHERE id='".$info['id']."'",'Checking Off');
				header("Location: room.php?key=". $_REQUEST['key']);
				die();
			}


			# Stuff In The Header
			function sith() { 
				global $BF;
?>		<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
		<script type='text/javascript'>
			function show_more() {
				var view_more = document.getElementById('more_room_data').style;
				var view_btn = document.getElementById('show_btn');
				if(view_more.display == 'none') {
					view_more.display = '';
					view_btn.value = 'Hide Room Details';
				} else {
					view_more.display = 'none';
					view_btn.value = 'Show More Details';
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