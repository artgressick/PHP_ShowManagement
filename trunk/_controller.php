<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Home");
			auth_check('litm');
			include_once($BF.'components/formfields.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
			}

			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;
		#################################################
		##	Log Out Page
		#################################################
		case 'logout.php':
			$title = "Logged Off";	# Page Title
			# Adding in the lib file
			include($BF .'_lib.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
			}
			# The template to use (should be the last thing before the break)
			include($BF ."models/nonav.php");		
			
			break;
		#################################################
		##	Error Page
		#################################################
		case 'error.php':
			$title = "Error Page";	# Page Title
			# Adding in the lib file
			include($BF .'_lib.php');
			include_once($BF.'components/formfields.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
			}

			# The template to use (should be the last thing before the break)
			include($BF ."models/nonav.php");		
			
			break;
 		#################################################
		##	Show Select Page
		#################################################
		case 'show.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Show Select");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','','0');
			include($BF.'components/formfields.php');

			if(isset($_POST['show_id'])) { 
				$show = db_query("SELECT id, show_name FROM shows WHERE !deleted AND id='".$_POST['show_id']."'","getting shows",1);
				$_SESSION['show_name'] = $show['show_name'];
				$_SESSION['show_id'] = $show['id'];
				setcookie("show_id", $show['id'], time()+60*60*24, '/');  /* expire in 1 day */
				$_COOKIE['show_id'] = $show['id'];
				$url = (isset($_SESSION['login_url'])?$_SESSION['login_url']:$BF.'index.php');
				unset($_SESSION['login_url']);
				header("Location: ".$url);
				die();
			} else if(isset($_COOKIE['show_id']) && is_numeric($_COOKIE['show_id']) && !isset($_REQUEST['s'])) {
				$show = db_query("SELECT id, show_name FROM shows WHERE !deleted AND id='".$_COOKIE['show_id']."'","getting shows",1);
				$_SESSION['show_name'] = $show['show_name'];
				$_SESSION['show_id'] = $show['id'];
				$url = (isset($_SESSION['login_url'])?$_SESSION['login_url']:$BF.'index.php');
				unset($_SESSION['login_url']);
				header("Location: ".$url);
				die();
			}

			# Stuff In The Header
			function sith() { 
				global $BF;
?>
	<script type='text/javascript' src='error_check.js'></script>
<?
			}


			$page_info['title'] = "Show Select";
			$page_info['instructions'] = "Select a Show to continue.";

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