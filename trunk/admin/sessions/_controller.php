<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Session";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/sessions/','TEXT'=>"Session Management");
	$time_slots = 10;
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section." List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','1');
			include_once($BF.'components/formfields.php');

			$q = "SELECT classes.*, sessiontypes.sessiontype_name,
						(SELECT CONCAT(start_date,' ',start_time) as start_dt FROM time_slots WHERE class_id=classes.id AND !deleted ORDER BY start_date, prep_time,
						 start_time, end_time, strike_time LIMIT 1) as start_dt,
						(SELECT rooms.room_name 
						FROM time_slots 
						JOIN rooms ON time_slots.room_id=rooms.id
						WHERE !time_slots.deleted AND class_id=classes.id 
						ORDER BY start_date, prep_time, start_time, end_time, strike_time LIMIT 1) as room_name
					FROM classes
					JOIN sessiontypes on classes.sessiontype_id = sessiontypes.id
					WHERE !classes.deleted
					ORDER BY start_dt, classes.class_name";
				
			$results = db_query($q,"getting classes");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "classes";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = $section." List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a Session to edit or add a new Session.";
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

 		#################################################
		##	Add Page
		#################################################
		case 'add.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Add ".$section);
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','1');
			include($BF.'components/formfields.php');

			if(isset($_POST['class_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS, $time_slots;
				include($BF .'components/list/sortlistjs.php');
?>		<script type="text/javascript" src="<?=$BF?>includes/forms.js"></script>
		<script type='text/javascript'>
		var page = 'add';
		var totalErrors = 0;
		function billing_check() {
			if(document.getElementById('bill_other0').checked==true) {
				document.getElementById('other_bill').style.display='none';
			} else {
				document.getElementById('other_bill').style.display='';
			}
		}
		function error_check() {
			if(totalErrors != 0) { reset_errors(); }  
			
			totalErrors = 0;
		
			if(errEmpty('class_name', "You must enter a Session Name.")) { totalErrors++; }
			if(errEmpty('sessioncat_id', "You must select a Session Category.")) { totalErrors++; }
			if(errEmpty('sessiontype_id', "You must select a Session Type.")) { totalErrors++; }
		<?
			$i = 0;
			while($i++ < $time_slots) {
		?>
			if(document.getElementById('date_slot_<?=$i?>').value != "") {
				if(errEmpty('start_time_slot_<?=$i?>', "You must enter a Start Time.")) { totalErrors++; }
				if(errEmpty('end_time_slot_<?=$i?>', "You must enter a End Time.")) { totalErrors++; }
			}
		<?
			}
		?>
			return (totalErrors == 0 ? true : false);
		}		
		
		function newOption(num,table) {
			var currentnum = parseInt(document.getElementById('int'+table).value) + 1;
			document.getElementById('int'+table).value = currentnum;
			
			var tr = document.createElement('tr');
			var td1 = document.createElement('td');
			var td2 = document.createElement('td');
			
			td1.innerHTML = "Diagram "+ currentnum +":";
			td2.id = table+"file"+ currentnum;
			td2.innerHTML = "<input type='file' name='chr"+table+"File"+ currentnum +"' id='chr"+table+"File"+ currentnum +"' />";
			
			tr.appendChild(td1);
			tr.appendChild(td2);
			document.getElementById(table+"tbody").appendChild(tr);
		}
	</script>
	<script src="<?=$BF?>components/cool_calendar/calendar.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-setup.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-en.js" type="text/javascript"></script>

<?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF,$time_slots;
				$i = 0;
?>
	<script type='text/javascript'>
<?
				while($i++ < $time_slots) {		
?>
		Calendar.setup({
			inputField     :    "date_slot_<?=$i?>",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
<?
				}				
?>
	</script>
<?
			}

			$page_info['title'] = "Add ".$section;
			$page_info['instructions'] = "Enter in all required information, and click one of the submit buttons below to save.";

			# The template to use (should be the last thing before the break)			
			include($BF ."models/template.php");	
			
			break;

		#################################################
		##	Edit Page
		#################################################
		case 'edit.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Edit ".$section);
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','1');
			include_once($BF.'components/formfields.php');
			
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM classes 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['class_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS, $time_slots;
				include($BF .'components/list/sortlistjs.php');
?>		<script type="text/javascript" src="<?=$BF?>includes/forms.js"></script>
		<script type='text/javascript'>
		var page = 'edit';
		var totalErrors = 0;
		function billing_check() {
			if(document.getElementById('bill_other0').checked==true) {
				document.getElementById('other_bill').style.display='none';
			} else {
				document.getElementById('other_bill').style.display='';
			}
		}

		function error_check() {
			if(totalErrors != 0) { reset_errors(); }  
			
			totalErrors = 0;
		
			if(errEmpty('class_name', "You must enter a Session Name.")) { totalErrors++; }
			if(errEmpty('sessioncat_id', "You must select a Session Category.")) { totalErrors++; }
			if(errEmpty('sessiontype_id', "You must select a Session Type.")) { totalErrors++; }
		<?
			$i = 0;
			while($i++ < $time_slots) {
		?>
			if(document.getElementById('date_slot_<?=$i?>').value != "") {
				if(errEmpty('start_time_slot_<?=$i?>', "You must enter a Start Time.")) { totalErrors++; }
				if(errEmpty('end_time_slot_<?=$i?>', "You must enter a End Time.")) { totalErrors++; }
			}
		<?
			}
		?>
			return (totalErrors == 0 ? true : false);
		}		
		function datetimesupdated() {
			document.getElementById('dtupdated').value = '1';
		}
		function newOption(num,table) {
			var currentnum = parseInt(document.getElementById('int'+table).value) + 1;
			document.getElementById('int'+table).value = currentnum;
			
			var tr = document.createElement('tr');
			var td1 = document.createElement('td');
			var td2 = document.createElement('td');
			
			td1.innerHTML = "Diagram "+ currentnum +":";
			td2.id = table+"file"+ currentnum;
			td2.innerHTML = "<input type='file' name='chr"+table+"File"+ currentnum +"' id='chr"+table+"File"+ currentnum +"' />";
			
			tr.appendChild(td1);
			tr.appendChild(td2);
			document.getElementById(table+"tbody").appendChild(tr);
		}
	</script>
	<script src="<?=$BF?>components/cool_calendar/calendar.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-setup.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-en.js" type="text/javascript"></script>

<?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF, $time_slots;
				$i = 0;
?>
	<script type='text/javascript'>
<?
				while($i++ < $time_slots) {		
?>
		Calendar.setup({
			inputField     :    "date_slot_<?=$i?>",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
<?
				}				
?>
	</script>
<?
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Edit ".$section.": ".$info['class_name'];
			$page_info['instructions'] = 'Please update the information below and press the "Update Information" when you are done making changes.';

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