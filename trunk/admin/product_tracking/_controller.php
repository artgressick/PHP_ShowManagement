<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Product Tracking";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/product_tracking/','TEXT'=>"Product Tracking");
    
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

			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
			if(!isset($_REQUEST['producttype_id'])) { $_REQUEST['producttype_id'] = ""; }
			if(!isset($_REQUEST['product_id'])) { $_REQUEST['product_id'] = ""; }


			$q = "SELECT product_tracking.*, CONCAT(products.product_name,' (',products.common_name,')') AS product_name, product_types.product_type, rooms.room_name, buildings.building_name,
						CONCAT(users.first_name,' ',users.last_name) as user_name
					FROM product_tracking
					JOIN products ON product_tracking.product_id=products.id
					JOIN product_types ON products.producttype_id=product_types.id
					JOIN rooms ON product_tracking.room_id=rooms.id
					JOIN buildings ON rooms.building_id=buildings.id
					JOIN users ON product_tracking.user_id=users.id
					WHERE product_tracking.check_in IS NULL AND product_tracking.show_id='".$_SESSION['show_id']."'
					". (isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" ) ."
					". (isset($_REQUEST['room_id']) && is_numeric($_REQUEST['room_id']) ? " AND rooms.id='".$_REQUEST['room_id']."'": "" ) ."
					". (isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND product_types.id='".$_REQUEST['producttype_id']."'": "" ) ."
					". (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id']) ? " AND products.id='".$_REQUEST['product_id']."'": "" ) ."

					ORDER BY product_tracking.check_out";
				
			$results = db_query($q,"getting tracking items");
			
			
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}

			# Stuff On The Bottom
			function sotb() { 
				global $BF;
				$tableName = "tracking";
				include($BF ."includes/overlay.php");
			}

			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");
			
			$product_types = db_query("SELECT id, product_type AS name FROM product_types WHERE !deleted AND enabled 
										AND id IN (SELECT producttype_id FROM products WHERE !deleted AND track_product)
										ORDER BY name","Get Product Types");
			
			$products = db_query("SELECT id, CONCAT(product_name,' (',common_name,')') AS name FROM products WHERE !deleted AND track_product AND !products.exclude AND enabled".(isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" )." ORDER BY name","Get Products");



			$page_info['title'] = $section." List";

			$page_info['instructions'] = "Lists All products that to be tracked that have not been checked-in.";
			$page_info['instructions'] .= "&nbsp;&nbsp;&nbsp;<input type='button' value='Check-Out' onclick='location.href=\"checkout.php\"' />";
			$page_info['instructions'] .= "&nbsp;&nbsp;&nbsp;<input type='button' value='Check-In' onclick='location.href=\"checkin.php\"' /><div style='padding-top:10px;'><strong>Filter: </strong>".
			form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?room_id=&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&room_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($product_types,array('caption'=>'All Product Types','nocaption'=>'true','name'=>'producttype_id','value'=>$_REQUEST['producttype_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&product_id=&producttype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($products,array('caption'=>'All Products','nocaption'=>'true','name'=>'product_id','value'=>$_REQUEST['product_id'],'extra'=>'onchange="location.href=\'?building_id='.$_REQUEST['building_id'].'&room_id='.$_REQUEST['room_id'].'&producttype_id='.$_REQUEST['producttype_id'].'&product_id=\'+this.value"','style'=>'width:100px;')).'</div>';
			
			
			# The template to use (should be the last thing before the break)
			include($BF ."models/template.php");		
			
			break;

 		#################################################
		##	Check-Out Page
		#################################################
		case 'checkout.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Check-Out Product(s)");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include($BF.'components/formfields.php');

			if(isset($_POST['email'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
?>	<script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script>
	<script type="text/javascript" src="<?=$BF?>includes/forms.js"></script>
	<script type='text/javascript'>
		var page = 'checkout';
		function checkfortracking() {
			if(document.getElementById('room_id').value != '' && document.getElementById('product_id').value != '') {
				document.getElementById('tracking_number').disabled = false;
				document.getElementById('tracking_number').focus();
			} else {
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
		function getroomassets() {
			var room_id = document.getElementById('room_id');
			if(room_id.value != "") {
				document.getElementById('spinner').style.display = '';
				get_room_assets('<?=$BF?>',room_id.value);
			} else {
				document.getElementById('room_assets').style.display = 'none';
				document.getElementById('asset_data').innerHTML = "";
			}
		}
		
	</script>
<?
			}


			$page_info['title'] = "Check-Out Products";
			$page_info['instructions'] = "Select Room, Product, and scan bar code.";

			# The template to use (should be the last thing before the break)			
			include($BF ."models/template.php");	
			
			break;

 		#################################################
		##	Check-In Page
		#################################################
		case 'checkin.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Check-In Product(s)");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include($BF.'components/formfields.php');

			if(isset($_POST['email'])) { include($post_file); }
			$bodyParams = "document.getElementById('tracking_number').focus();";
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


			$page_info['title'] = "Check-In Products";
			$page_info['instructions'] = "Scan the Bar code to Check-In a product.";

			# The template to use (should be the last thing before the break)			
			include($BF ."models/template.php");	
			
			break;

 		#################################################
		##	Reset Tracking Page
		#################################################
		case 'resettracking.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => "Correct Tracking");
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include($BF.'components/formfields.php');
			if(!isset($_REQUEST['producttype_id'])) { $_REQUEST['producttype_id'] = ""; }
			if(!isset($_REQUEST['product_id'])) { $_REQUEST['product_id'] = ""; }			
			if(!isset($_REQUEST['tracking_num'])) { $_REQUEST['tracking_num'] = ""; }

			$results = db_query("SELECT pt.product_id, pt.tracking_number
								FROM product_tracking AS pt
								JOIN products ON pt.product_id=products.id
								WHERE show_id = '".$_SESSION['show_id']."'
								". (isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" ) ."
								". (isset($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id']) ? " AND products.id='".$_REQUEST['product_id']."'": "" ) ."
								". (isset($_REQUEST['tracking_num']) && $_REQUEST['tracking_num'] != "" ? " AND pt.tracking_number LIKE '%".encode($_REQUEST['tracking_num'])."%'": "" ) ."								
								GROUP BY pt.tracking_number
								ORDER BY tracking_number
						","Getting Tracking Numbers");


			if(isset($_POST['rtracking'])) { include($post_file); }

			# Stuff In The Header
			function sith() { 
				global $BF;
				include($BF .'components/list/sortlistjs.php');
?>
	<script type='text/javascript'>
		function edit_number(tn) {
			var disp_val = document.getElementById('tn_'+tn);
			var edit_btn = document.getElementById('ed_'+tn);
			var save_btn = document.getElementById('sv_'+tn);
			var text_box = document.getElementById('ntn_'+tn);
			disp_val.style.display = "none";
			edit_btn.style.display = "none";
			save_btn.style.display = "";
			text_box.style.display = "";
		}
		function save_number(tn) {
			var disp_val = document.getElementById('tn_'+tn);
			var edit_btn = document.getElementById('ed_'+tn);
			var save_btn = document.getElementById('sv_'+tn);
			var text_box = document.getElementById('ntn_'+tn);
			disp_val.innerHTML = text_box.value;
			disp_val.style.display = "";
			edit_btn.style.display = "";
			save_btn.style.display = "none";
			text_box.style.display = "none";
		}

	</script>

<?				
			}


			$product_types = db_query("SELECT id, product_type AS name FROM product_types WHERE !deleted AND enabled 
										AND id IN (SELECT producttype_id FROM products WHERE !deleted AND track_product)
										ORDER BY name","Get Product Types");
			
			$products = db_query("SELECT id, CONCAT(product_name,' (',common_name,')') AS name FROM products WHERE !deleted AND track_product AND !products.exclude AND enabled".(isset($_REQUEST['producttype_id']) && is_numeric($_REQUEST['producttype_id']) ? " AND products.producttype_id='".$_REQUEST['producttype_id']."'": "" )." ORDER BY name","Get Products");


			$page_info['title'] = "Correct Tracking";
			$page_info['instructions'] = "To correct a tracking number select the correct product for that tracking number and click Save.<br />";

			$page_info['instructions'] = "Filter: ".
			
			form_select($product_types,array('caption'=>'All Product Types','nocaption'=>'true','name'=>'producttype_id','value'=>$_REQUEST['producttype_id'],'extra'=>'onchange="location.href=\'?product_id=&tracking_num='.$_REQUEST['tracking_num'].'&producttype_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".
			
			form_select($products,array('caption'=>'All Products','nocaption'=>'true','name'=>'product_id','value'=>$_REQUEST['product_id'],'extra'=>'onchange="location.href=\'?producttype_id='.$_REQUEST['producttype_id'].'&tracking_num='.$_REQUEST['tracking_num'].'&product_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".

			form_text(array('caption'=>'Tracking Number','nocaption'=>'true','name'=>'tracking_num','value'=>$_REQUEST['tracking_num'],'extra'=>'onchange="location.href=\'?producttype_id='.$_REQUEST['producttype_id'].'&product_id='.$_REQUEST['product_id'].'&tracking_num=\'+this.value"','style'=>'width:100px;'));


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