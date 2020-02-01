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
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Administration Home");
			auth_check('litm','admin','0');
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
		##	Master Import
		#################################################
		case 'master_import.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Master Import");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include_once($BF.'components/formfields.php');

			# Stuff In The Header
			function sith() { 
				global $BF;
?>		<script type='text/javascript'>
			function start_import() {
				document.getElementById('start_btn').disabled=true;
				document.getElementById('log_window').src = '<?=$BF?>_master_xml_import.php';
			}
		</script>		
<?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			$page_info['title'] = "Master Import";
			$page_info['instructions'] = "This will run a full import from ISTE's XML file into Showman.  This can take up to a few minutes, please only do this if you are sure there is light site traffic.  This process is also done automatically every night at midnight.";
			
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