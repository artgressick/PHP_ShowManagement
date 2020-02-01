<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "User";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/users/','TEXT'=>"User Management");
    
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

			$q = "SELECT users.*, groups.group_name
					FROM users
					JOIN groups ON users.group_id=groups.id
					WHERE !deleted
					ORDER BY users.last_name, users.first_name";
				
			$results = db_query($q,"getting users");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "users";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = $section." List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a user to edit their account or add a new user to have access to this software.";
			
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

			if(isset($_POST['email'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
?>	<script type='text/javascript'>var page = 'add';</script>
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
								FROM users 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['email'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
			
?>	<script type='text/javascript'>var page = 'edit';</script>
	<script type='text/javascript' src='error_check.js'></script>
<?

			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Edit ".$section.": ".$info['first_name'].' '.$info['last_name'];
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