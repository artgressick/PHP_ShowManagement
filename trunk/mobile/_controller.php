<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../';
	
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
			include($BF ."models/mobile.php");		
			
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
			include($BF ."models/mnonav.php");		
			
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
			include($BF ."models/mnonav.php");		
			
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
				$show = db_query("SELECT id, show_name FROM shows WHERE !deleted AND id=".$_POST['show_id'],"getting shows",1);
				$_SESSION['show_name'] = $show['show_name'];
				$_SESSION['show_id'] = $show['id'];
				header("Location: index.php");
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
			include($BF ."models/mnonav.php");	
			
			break;
		#################################################
		##	Check Out Product Page
		#################################################
		case 'checkout.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm','admin','1');
			include_once($BF.'components/formfields.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
?>	<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
	<script type="text/javascript" src="<?=$BF?>includes/forms.js"></script>
	<script type='text/javascript'>
		var page = 'checkout';
		function checkfortracking() {
			if(document.getElementById('room_id').value != '' && document.getElementById('product_id').value != '') {
				document.getElementById('tracking_number').value = '';
				document.getElementById('tracking_number').disabled = false;
				document.getElementById('tracking_number').focus();
			} else {
				document.getElementById('tracking_number').value = 'Select Room and Product';
				document.getElementById('tracking_number').disabled = true;
			}
		}
		function submittracking() {
			reset_errors();
			var room_id = document.getElementById('room_id');
			var product_id = document.getElementById('product_id');
			var tracking_num = document.getElementById('tracking_number');
			if(room_id.value != '' && product_id.value != '' && tracking_num.value != '') {
				check_out_product('<?=$BF?>', room_id.value, product_id.value, tracking_num.value);
			} else {
				setErrorMsg('Please try scanning again');
				trackin_num.value = ''
				trackin_num.focus();
			}
		}
	</script>
<?
			}

			# The template to use (should be the last thing before the break)
			include($BF ."models/mobile.php");		
			
			break;
		#################################################
		##	Check In Product Page
		#################################################
		case 'checkin.php':
			# Adding in the lib file
			include($BF .'mobile/_lib.php');
			auth_check('litm','admin','1');
			include_once($BF.'components/formfields.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
?>	<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
	<script type="text/javascript" src="<?=$BF?>includes/forms.js"></script>
	<script type='text/javascript'>
		var page = 'checkin';
		function submittracking() {
			reset_errors();
			var tracking_num = document.getElementById('tracking_number');
			if(tracking_num.value != '') {
				check_in_product('<?=$BF?>', tracking_num.value);
			} else {
				setErrorMsg('Please try scanning again');
				trackin_num.value = ''
				trackin_num.focus();
			}
		}
	</script>
	<script type='text/javascript' src='error_check.js'></script>
<?
			}

			# The template to use (should be the last thing before the break)
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