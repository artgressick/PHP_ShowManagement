<?
	# This is the BASE FOLDER pointing back to the root directory
	$BF = '../../';
	
	preg_match('/(\w)+\.php$/',$_SERVER['SCRIPT_NAME'],$file_name);
    $post_file = '_'.$file_name[0];
    $breadcrumbs = array();
    $section = "Product Type Questions";
	$breadcrumbs[] = array('URL' => $BF.'admin/', 'TEXT' => "Administration");
	$breadcrumbs[] = array('URL' => $BF.'admin/pt_questions/','TEXT'=>"Product Type Questions");
    
	switch($file_name[0]) {
		#################################################
		##	Index Page
		#################################################
		case 'index.php':
			# Adding in the lib file
			include($BF .'_lib.php');
			$breadcrumbs[] = array('TEXT' => $section);
			# Auth Check, enable this if the page requires you to be logged in
			auth_check('litm','admin','0');
			include_once($BF.'components/formfields.php');

			$q = "SELECT sp.id, CONCAT(st.sessiontype_name,' ==> ',pt.product_type) AS association
					FROM sessiontype_producttypes AS sp
					JOIN sessiontypes AS st ON sp.sessiontype_id=st.id
					JOIN product_types AS pt ON sp.producttype_id=pt.id
					WHERE !st.deleted AND !pt.deleted
					ORDER BY association
			";
				
			$results = db_query($q,"getting shows");
			# Stuff In The Header
			function sith() { 
				global $BF;
				?><script type='text/javascript' src='<?=$BF?>includes/overlays.js'></script><?
				include($BF .'components/list/sortlistjs.php');
			}



			$page_info['title'] = $section;
			$page_info['instructions'] = "Select a association to add/edit questions";
			
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
			if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) { errorPage('Invalid Association'); } // Check Required Field for Query

			$info = db_query("
								SELECT sp.id, CONCAT(st.sessiontype_name,' ==> ',pt.product_type) AS association, sessiontype_id, producttype_id
								FROM sessiontype_producttypes AS sp 
								JOIN sessiontypes AS st ON sp.sessiontype_id=st.id
								JOIN product_types AS pt ON sp.producttype_id=pt.id
								WHERE !st.deleted AND !pt.deleted AND sp.id='". $_REQUEST['id'] ."'
			","getting info",1); // Get Info
				
			if($info['id'] == "") { errorPage('Invalid Association'); } // Did we get a result?
			
			if(count($_POST)) { include($post_file); }

			$type_results = db_query("SELECT id, fieldtype, description FROM fieldtypes WHERE !deleted ORDER BY id","getting field types");	
			
			$types = '<option value="">-Select Option Type-</option>';
			$messages = array();
			while($row = mysqli_fetch_row($type_results)) {
				$types .= '<option value="'.$row[0] .'">'.$row[1].'</option>';
				$messages[$row[0]] = $row[2];
			}
			$types .= "</select>";

			# Stuff In The Header
			function sith() { 
				global $BF,$PROJECT_ADDRESS, $types, $messages;
			
?>	<link href="<?=$BF?>includes/dynamic.css" rel="stylesheet" type="text/css" />
	<script type='text/javascript'>var page = 'edit';</script>
	<script type='text/javascript' src='error_check.js'></script>
	<script type='text/javascript'>
		var types = '<?=$types?>';
		var messages = new Array('','<?=implode("','",$messages)?>');
		function showOptions(type,num) {
			var box = document.getElementById('options'+num);
			box.className = "additional";
			if(type == 1) {
				// Header
				box.innerHTML = "<?=(isset($messages['1'])?$messages['1']:'')?>";
				document.getElementById('required'+num).disabled=true;
			} else if(type == 2) {
				// Sentance Box
				box.innerHTML = "<?=(isset($messages['2'])?$messages['2']:'')?>";
				document.getElementById('required'+num).disabled=false;
			} else if(type == 3) {
				// Private Box
				box.innerHTML = "<?=(isset($messages['3'])?$messages['3']:'')?>";
				document.getElementById('required'+num).disabled=false;
			} else if(type == 4) {
				// Paragraph Box
				box.innerHTML = "<?=(isset($messages['4'])?$messages['4']:'')?>";
				document.getElementById('required'+num).disabled=false;
			} else if(type == 5) {
				// Select Box
				box.innerHTML = "<?=(isset($messages['5'])?$messages['5']:'')?>" +
				fillOptions(num);
				document.getElementById('required'+num).disabled=false;
			} else if(type == 6) {
				// Check Box
				box.innerHTML = "<?=(isset($messages['6'])?$messages['6']:'')?>" +
				fillOptions(num);
				document.getElementById('required'+num).disabled=false;
			} else if(type == 7) {
				// Radio Buttons
				box.innerHTML = "<?=(isset($messages['7'])?$messages['7']:'')?>" +
				fillOptions(num);
				document.getElementById('required'+num).disabled=false;
			}
		}
		
		function fillOptions(num) {
			return "<div id='optionset"+ num +"'>" +
					"<table id='optionsetTbl"+ num +"' cellpadding='0' cellspacing='0'><tr><td class='optionlabel'>Option 1:</td><td class='optionBox' id='optionBox"+ num +"-1'><input type='text' name='optionval"+ num +"-1' id='optionval"+ num +"-1' />" +
					"</td><input type='hidden' name='optionval"+ num +"' id='optionval"+ num +"' value='1' /><td class='optionExtra'><input type='button' id='removeOption"+ num +"-1' onclick='javascript:eraseOption(\""+ num +"-1\")' value='Remove Option' /></td></tr></table><div><input type='button' onclick='javascript:newOption("+ num +");' value='Add Another Option' /></div>";
		}
		
		function addNew() {
			num = document.getElementById('count');
			num.value = parseInt(num.value) + 1;
		
			var div = document.createElement('div');
			
			div.innerHTML = '<table cellspacing="0" cellpadding="0" class="questions" id="question'+ num.value +'">' +
			'	<tr>' +
			'		<td class="lheader"><strong>Question '+ num.value +'</strong></td>' +
			'		<td class="loption"><input type="text"" name="question'+ num.value +'" id="question'+ num.value +'"" style="width: 325px;"" /></td>' +
			'		<td class="rheader">Required Field</td>' +
			'		<td class="roption"><input type="checkbox" name="required'+ num.value +'" id="required'+ num.value +'" /></td>' +
			'	</tr>' +
			'	<tr>' +
			'		<td class="lheader">Answer Option Types:</td>' +
			'		<td class="loption"><select name="fieldtype_id'+ num.value +'" id="fieldtype_id'+ num.value +'" onchange="showOptions(this.value,'+ num.value +')">'+ types +'</td>' +
			'		<td class="rheader">Display Order</td>' +
			'		<td class="roption"><input type="text" name="sort_order'+ num.value +'" id="sort_order'+ num.value +'" value="'+ num.value +'" style="width: 25px;" /></td>' +
			'	</tr>' +
			'	<tr>' +
			'		<td colspan="4" id="options'+ num.value +'"></td>' +
			'	</tr>' +
			'</table>'+
			'<input type="hidden" name="deleted'+ num.value +'" id="deleted'+ num.value +'" value="0" />' +
			'<div style="text-align: right; padding-top:2px;"><input type="button" value="Remove Question '+ num.value +'" onclick="eraseQuestion('+ num.value +');" id="addremove'+ num.value +'" /></div>';
			
			document.getElementById('questions').appendChild(div);
		}
		
		function eraseQuestion(num) {
			var val = document.getElementById('question'+num);
			if(val.style.display == 'none') {
				val.style.display = '';
				document.getElementById('addremove'+num).value = 'Remove Question '+num;
				document.getElementById('deleted'+num).value = '0';
			} else {
				val.style.display = 'none';
				document.getElementById('addremove'+num).value = 'Re-Add Question '+num;
				document.getElementById('deleted'+num).value = '1';
			}
		}
		
		function eraseOption(num) {
			var val = document.getElementById('removeOption'+num);
			if(document.getElementById('removedVal'+num)) {
				var remVal = document.getElementById('removedVal'+num).innerHTML;
				document.getElementById('optionBox'+num).innerHTML = "<input type='text' name='optionval"+num+"' id='optionval"+num+"' value='"+ remVal +"' />";
				document.getElementById('removeOption'+num).value = "Remove Option";
			} else {
				var tmpVal = document.getElementById('optionval'+num).value;
				document.getElementById('optionBox'+num).innerHTML = "Option Removed<span id='removedVal"+num+"' style='display:none;'>"+tmpVal+"</span>";
				document.getElementById('removeOption'+num).value = "Re-Enable Option";
			}
		}
		
		function newOption(num) {
			var currentnum = document.getElementById('optionval'+num);
			currentnum.value = parseInt(currentnum.value) + 1;
			
			var tr = document.createElement('tr');
			var td1 = document.createElement('td');
			var td2 = document.createElement('td');
			var td3 = document.createElement('td');
			td1.className='optionlabel';
			td1.innerHTML = "<td>Option "+ currentnum.value +":</td>";
			
			td2.className='optionBox';
			td2.id='optionBox'+ num +'-'+ currentnum.value;
			td2.innerHTML="<input type='text' name='optionval"+ num +"-"+ currentnum.value +"' id='optionval"+ num +"-"+ currentnum.value +"' /></td>";
		
			td3.className='optionExtra';
			td3.innerHTML = "<input type='button' id='removeOption"+ num +"-"+ currentnum.value +"' onclick='eraseOption(\""+ num +"-"+ currentnum.value +"\")' value='Remove Option' /></td>";
			
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			document.getElementById("optionsetTbl"+ num).appendChild(tr);
		}
		  
	
	</script>
<?
			}
			# Stuff On The Bottom
			function sotb() { 
				global $BF;
			}

			# The template to use (should be the last thing before the break)
			
			$page_info['title'] = "Questions for ".$info['association'];
			$page_info['instructions'] = 'Please add/update the information below and press the "Save Questions" when you are done making changes.';

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