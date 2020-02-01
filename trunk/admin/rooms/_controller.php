<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Room";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/rooms/','TEXT'=>"Room Management");
    
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

			$q = "SELECT rooms.*, buildings.building_name
					FROM rooms
					JOIN buildings ON rooms.building_id=buildings.id
					WHERE !rooms.deleted AND rooms.show_id=".$_SESSION['show_id']."
					ORDER BY rooms.room_number";
				
			$results = db_query($q,"getting rooms");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "rooms";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = $section." List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a Room to edit or add a new Room. ".'<input type="button" value="Product Mass Add" onclick="location.href=\'mass_add.php\'" />';
			
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

			if(isset($_POST['room_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS;
?>	<script type='text/javascript'>
		var page = 'add';
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
	<script type='text/javascript' src='error_check.js'></script>
	<script src="<?=$BF?>components/cool_calendar/calendar.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-setup.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-en.js" type="text/javascript"></script>

<?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
?>
	<script type='text/javascript'>
		Calendar.setup({
			inputField     :    "move_in-date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "move_out-date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
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
								FROM rooms 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['room_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
			
?>	<script type='text/javascript'>
		var page = 'edit';
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
	<script type='text/javascript' src='error_check.js'></script>
	<script src="<?=$BF?>components/cool_calendar/calendar.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-setup.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-en.js" type="text/javascript"></script>

<?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
?>
	<script type='text/javascript'>
		Calendar.setup({
			inputField     :    "move_in-date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "move_out-date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
	</script>
<?
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Edit ".$section.": ".$info['room_name'];
			$page_info['instructions'] = 'Please update the information below and press the "Update Information" when you are done making changes.';

			include($BF ."models/template.php");		
			
			break;

		#################################################
		##	Products Page
		#################################################
		case 'products.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section." Products");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','1');
			include_once($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM rooms 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?

			$q = "SELECT room_products.*, products.product_name, product_types.product_type, products.needs_quantity
					FROM room_products
					JOIN products ON room_products.product_id=products.id
					JOIN product_types ON products.producttype_id = product_types.id
					WHERE !room_products.deleted AND products.enabled AND product_types.enabled AND room_products.room_id=".$info['id']."
					ORDER BY products.product_name";
				
			$results = db_query($q,"getting rooms");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "room_products";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = "Standard Room Products for: ".$info['room_name'];
			$page_info['add_link'] = "add_product.php?key=".$info['lkey'];
			$page_info['instructions'] = "Add, Remove, or Update the Quantity of Products for this room.";
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

 		#################################################
		##	Add Product Page
		#################################################
		case 'add_product.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Add Room Product");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','1');
			include($BF.'components/formfields.php');

			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM rooms 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?

			if(isset($_POST['product_id'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			$page_info['title'] = "Add Room Products to: ".$info['room_name'];
			$page_info['instructions'] = "Select a Product and enter a quantity. then click one of the submit buttons below to save.";

			# The template to use (should be the last thing before the break)			
			include($BF ."models/template.php");	
			
			break;

 		#################################################
		##	Mass Add Product Page
		#################################################
		case 'mass_add.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Add Room Product");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','1');
			include($BF.'components/formfields.php');

			if(isset($_POST['products'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS;
				include($BF .'components/list/sortlistjs.php');
?>
		<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
		<script type='text/javascript'>
			function check_product(id) {
				if(document.getElementById('product'+id).checked==true) {
					document.getElementById('qty'+id).value = 1;
					if(document.getElementById('qty'+id).type == 'text') {
						document.getElementById('qty'+id).disabled=false;
						document.getElementById('qty'+id).focus();
					}
				} else {
					document.getElementById('qty'+id).value='';
					if(document.getElementById('qty'+id).type == 'text') {
						document.getElementById('qty'+id).disabled=true;
					}
				}
			}
			function checkvalue(id) {
				if(!IsWhole(document.getElementById('qty'+id).value) || document.getElementById('qty'+id).value == 0) {
					document.getElementById('qty'+id).value = 1;
				}
			}
		</script>
<?

			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			$page_info['title'] = "Mass Add Products to Rooms";
			$page_info['instructions'] = "Select the Products from the left column, enter quantity if needed, Select all rooms you would like that product to appear in, NOTE. if that product is already in that room it will be skipped.";

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