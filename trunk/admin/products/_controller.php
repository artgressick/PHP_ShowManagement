<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Product";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/products/','TEXT'=>"Products");
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			if(isset($_REQUEST['export'])) { $NON_HTML_PAGE=1; }
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section." List");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include_once($BF.'components/formfields.php');

			$q = "SELECT products.*, product_types.product_type
					FROM products
					JOIN product_types ON products.producttype_id=product_types.id
					WHERE !products.deleted AND !product_types.deleted
					ORDER BY products.product_name";
				
			$results = db_query($q,"getting Products");
			
			if(isset($_REQUEST['export'])) { include($post_file); }
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "products";
				include($BF ."includes/overlay.php");
			}

			$page_info['title'] = $section." List";
			$page_info['add_link'] = "add.php";
			$page_info['instructions'] = "Click a Product to edit or add a new Product.";
			$page_info['title_right'] = form_button(array('type'=>'button','value'=>'Export to Excel','extra'=>'onclick="location.href=\'?&export=true\'"'));

			
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

			if(isset($_POST['product_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF, $PROJECT_ADDRESS;
?>	<script type='text/javascript'>
		var page = 'add';
		function checkall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					document.getElementById(name+ids[i]).checked = true;
				}
			}
		}
		function uncheckall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					document.getElementById(name+ids[i]).checked = false;
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
								FROM products 
								WHERE lkey='". $_REQUEST['key'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid '.$section); } // Did we get a result?
			
			if(isset($_POST['product_name'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
			
?>	<script type='text/javascript'>
		var page = 'edit';
		function checkall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					document.getElementById(name+ids[i]).checked = true;
					document.getElementById('updated_sessiontypes').value=1
				}
			}
		}
		function uncheckall(name) {
			var tids = document.getElementById('ids').value;
			var ids = tids.split(",");
			for ( var i in ids ) {
				if(document.getElementById(name+ids[i])) {
					document.getElementById(name+ids[i]).checked = false;
					document.getElementById('updated_sessiontypes').value=1
				}
			}
		}
	</script>
	<script type='text/javascript' src='error_check.js'></script>
<?
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Edit ".$section.": ".$info['product_name'];
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