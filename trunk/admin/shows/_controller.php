<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Show";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/shows/','TEXT'=>"Show Management");
    
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

			$q = "SELECT shows.*, show_status.status_name
					FROM shows
					JOIN show_status ON shows.status_id=show_status.id
					WHERE !shows.deleted
					ORDER BY shows.start_date, shows.show_name";
				
			$results = db_query($q,"getting shows");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}



			$page_info['title'] = $section." List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a show to edit or add a new show.";
			
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

			if(isset($_POST['show_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS;
?>
	<script type='text/javascript'>
		var page = 'add';
	</script>
	<script src="<?=$BF?>components/cool_calendar/calendar.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-setup.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-en.js" type="text/javascript"></script>
	<script type='text/javascript' src='error_check.js'></script>
	<script type="text/javascript" src="<?=$BF?>components/tiny_mce/tiny_mce_gzip.js"></script>
				<script type="text/javascript">
				tinyMCE_GZ.init({
					plugins : 'style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
					themes : 'simple,advanced',
					languages : 'en',
					disk_cache : true,
					debug : false
				});
				</script>
				<!-- Needs to be seperate script tags! -->
				<script language="javascript" type="text/javascript">
					tinyMCE.init({
						mode : "textareas",
						plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,filemanager",
						theme_advanced_buttons1_add : "fontselect,fontsizeselect",
						theme_advanced_buttons2_add : "separator,forecolor,backcolor",
						theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
						theme_advanced_buttons3_add : "emotions,flash,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
						theme_advanced_toolbar_location : "top",
						theme_advanced_path_location : "bottom",
						content_css : "/example_data/example_full.css",
					    plugin_insertdate_dateFormat : "%Y-%m-%d",
					    plugin_insertdate_timeFormat : "%H:%M:%S",
						extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
						external_link_list_url : "example_data/example_link_list.js",
						external_image_list_url : "example_data/example_image_list.js",
						flash_external_list_url : "example_data/example_flash_list.js",
						file_browser_callback : "mcFileManager.filebrowserCallBack",
						theme_advanced_resize_horizontal : false,
						theme_advanced_resizing : true,
						apply_source_formatting : true,
						
						filemanager_rootpath : "<?=realpath($BF . 'files/')?>",
						filemanager_path : "<?=realpath($BF . 'files/')?>",
						filemanager_extensions : "gif,jpg,htm,html,pdf,zip,txt,doc,xls",
						relative_urls : true,
						document_base_url : "<?=$PROJECT_ADDRESS?>"
					});
				</script><?
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
?>
	<script type='text/javascript'>
		Calendar.setup({
			inputField     :    "start_date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "end_date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "lock_requests",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "show_room_data",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "show_signoff",     // id of the input field
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
			auth_check('litm','admin','0');
			include_once($BF.'components/formfields.php');
			
			
			# Check for KEY, if not Error, Get $info, Error if no results
			if(!isset($_REQUEST['key']) || $_REQUEST['key'] == "") { errorPage('Invalid '.$section); } // Check Required Field for Query

			$info = db_query("
								SELECT * 
								FROM shows 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['show_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF,$PROJECT_ADDRESS;
			
?>	<script type='text/javascript'>var page = 'edit';</script>
	<script type='text/javascript' src='error_check.js'></script>
	<script src="<?=$BF?>components/cool_calendar/calendar.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-setup.js" type="text/javascript"></script>
	<script src="<?=$BF?>components/cool_calendar/calendar-en.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?=$BF?>components/tiny_mce/tiny_mce_gzip.js"></script>
				<script type="text/javascript">
				tinyMCE_GZ.init({
					plugins : 'style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
					themes : 'simple,advanced',
					languages : 'en',
					disk_cache : true,
					debug : false
				});
				</script>
				<!-- Needs to be seperate script tags! -->
				<script language="javascript" type="text/javascript">
					tinyMCE.init({
						mode : "textareas",
						plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,filemanager",
						theme_advanced_buttons1_add : "fontselect,fontsizeselect",
						theme_advanced_buttons2_add : "separator,forecolor,backcolor",
						theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
						theme_advanced_buttons3_add : "emotions,flash,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
						theme_advanced_toolbar_location : "top",
						theme_advanced_path_location : "bottom",
						content_css : "/example_data/example_full.css",
					    plugin_insertdate_dateFormat : "%Y-%m-%d",
					    plugin_insertdate_timeFormat : "%H:%M:%S",
						extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
						external_link_list_url : "example_data/example_link_list.js",
						external_image_list_url : "example_data/example_image_list.js",
						flash_external_list_url : "example_data/example_flash_list.js",
						file_browser_callback : "mcFileManager.filebrowserCallBack",
						theme_advanced_resize_horizontal : false,
						theme_advanced_resizing : true,
						apply_source_formatting : true,
						
						filemanager_rootpath : "<?=realpath($BF . 'files/')?>",
						filemanager_path : "<?=realpath($BF . 'files/')?>",
						filemanager_extensions : "gif,jpg,htm,html,pdf,zip,txt,doc,xls",
						relative_urls : true,
						document_base_url : "<?=$PROJECT_ADDRESS?>"
					});
				</script><?
			}
			# Stuff On The Bottom
			function sotb() { 
				global $BF;
?>
	<script type='text/javascript'>
		Calendar.setup({
			inputField     :    "start_date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "end_date",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "lock_requests",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "show_room_data",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});
		Calendar.setup({
			inputField     :    "show_signoff",     // id of the input field
			ifFormat       :    "%Y-%m-%d",      // format of the input field
			align          :    "Bl",           // alignment (defaults to "Bl")
			singleClick    :    true
		});

	</script>
<?
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Edit ".$section.": ".$info['show_name'];
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