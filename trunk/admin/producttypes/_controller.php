<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Product Type";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/producttypes/','TEXT'=>"Product Types");
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section." List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include_once($BF.'components/formfields.php');

			$q = "SELECT product_types.*
					FROM product_types
					WHERE !product_types.deleted
					ORDER BY product_types.product_type";
				
			$results = db_query($q,"getting Product Types");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "product_types";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = $section." List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a Product Type to edit or add a new Product Type.";
			
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
			auth_check('litm','admin','0');
			include($BF.'components/formfields.php');

			if(isset($_POST['product_type'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS;
?>	<script type='text/javascript'>
		var page = 'add';
		function check(id) {
			if(document.getElementById('allow'+id).checked==true) {
				document.getElementById('custom'+id).disabled = false;
			} else {
				document.getElementById('custom'+id).checked = false;
				document.getElementById('custom'+id).disabled = true;
			}	
		}
		function checkall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					if(name == 'custom') {
						if(document.getElementById('allow'+ids[i]).checked == true) {
							document.getElementById(name+ids[i]).checked = true;
						}
					} else {
						document.getElementById(name+ids[i]).checked = true;
						check(ids[i]);
					}
				}
			}
		}
		function uncheckall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					document.getElementById(name+ids[i]).checked = false;
					check(ids[i]);
				}
			}
		}

	</script>
	<script type='text/javascript' src='error_check.js'></script>
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
			auth_check('litm','admin','0');
			include_once($BF.'components/formfields.php');
			
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM product_types 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['product_type'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
			
?>	<script type='text/javascript'>
		var page = 'edit';
		function check(id) {
			if(document.getElementById('allow'+id).checked==true) {
				document.getElementById('custom'+id).disabled = false;
				document.getElementById('typechanged').value = 1;
			} else {
				document.getElementById('custom'+id).checked = false;
				document.getElementById('custom'+id).disabled = true;
				document.getElementById('typechanged').value = 1;
			}	
		}
		function markchange() {
			document.getElementById('typechanged').value = 1;
		}
		function checkall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					if(name == 'custom') {
						if(document.getElementById('allow'+ids[i]).checked == true) {
							document.getElementById(name+ids[i]).checked = true;
						}
					} else {
						document.getElementById(name+ids[i]).checked = true;
						check(ids[i]);
					}
				}
			}
		}
		function uncheckall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					document.getElementById(name+ids[i]).checked = false;
					check(ids[i]);
				}
			}
		}

	</script>
	<script type='text/javascript' src='error_check.js'></script>
<?
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Edit ".$section.": ".$info['product_type'];
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