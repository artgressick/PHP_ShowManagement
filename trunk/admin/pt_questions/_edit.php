<?
	include_once($BF.'components/edit_functions.php');
	include_once($BF.'components/add_functions.php');

	$q = "";
	$i = 0;
	while($i++ <= $_POST['count']) {
		if(isset($_POST['QID-'.$i]) && is_numeric($_POST['QID-'.$i])) {
			if(isset($_POST['question'.$i]) && $_POST['question'.$i] != '') {
				$optionVals = '';
				if(!in_array($_POST['fieldtype_id'.$i],array(1,2,3,4))) {
					$j = 0;
					# Create a ||| seperated list of options.
					while($j++ <= $_POST['optionval'.$i]) {
						$optionVals .= (isset($_POST['optionval'.$i.'-'.$j]) && $_POST['optionval'.$i.'-'.$j] != "" ? encode($_POST['optionval'.$i.'-'.$j]).'|||' : '');
					}
				}
				$query = "UPDATE pt_questions SET 
						deleted='".$_POST['deleted'.$i]."',
						required='".(isset($_POST['required'.$i]) && $_POST['required'.$i] == "on" ? 1 : 0)."',
						fieldtype_id='".$_POST['fieldtype_id'.$i]."',
						sort_order='".$_POST['sort_order'.$i]."',
						question='".encode($_POST['question'.$i])."',
						options='".substr($optionVals,0,-3)."'
						WHERE id=".$_POST['QID-'.$i]."
				";
			} else if($_POST['fieldtype_id'.$i] != 1) {
				$optionVals = '';
				if(!in_array($_POST['fieldtype_id'.$i],array(1,2,3,4))) {
					$j = 0;
					# Create a ||| seperated list of options.
					while($j++ <= $_POST['optionval'.$i]) {
						$optionVals .= (isset($_POST['optionval'.$i.'-'.$j]) && $_POST['optionval'.$i.'-'.$j] != "" ? encode($_POST['optionval'.$i.'-'.$j]).'|||' : '');
					}
				}
				$query = "UPDATE pt_questions SET 
						deleted='1',
						required='".(isset($_POST['required'.$i]) && $_POST['required'.$i] == "on" ? 1 : 0)."',
						fieldtype_id='".$_POST['fieldtype_id'.$i]."',
						sort_order='".$_POST['sort_order'.$i]."',
						question='".encode($_POST['question'.$i])."',
						txtOptions='".substr($optionVals,0,-3)."'
						WHERE id=".$_POST['QID-'.$i]."
				";
			} else {
				$query = "DELETE FROM pt_questions WHERE id=".$_POST['QID-'.$i]."";
			}
			if($query != '') {
				db_query($query,"Update Question");
			}
		} else {
			# First, make sure that the question is set AND that it wasn't set to be removed.
			if(isset($_POST['question'.$i]) && $_POST['question'.$i] != '' && $_POST['deleted'.$i] != 1) {
				
				# If they chose a text (1) or textarea (2) field, continue, else run a few more checks.
				if(in_array($_POST['fieldtype_id'.$i],array(1,2,3,4))) {
					$q .= "('". $info['sessiontype_id'] ."','". $info['producttype_id'] ."','". (isset($_POST['required'.$i]) && $_POST['required'.$i] == "on" ? 1 : 0) ."','". $_POST['fieldtype_id'.$i] ."','". $_POST['sort_order'.$i] ."','". encode($_POST['question'.$i]) ."','','".$_SESSION['show_id']."'),";	
				} else {
					$optionVals = "";
					$j = 0;
					# Create a ||| seperated list of options.
					while($j++ <= $_POST['optionval'.$i]) {
						if(isset($_POST['optionval'.$i.'-'.$j])) {
							$optionVals .= ($_POST['optionval'.$i.'-'.$j] != "" ? encode($_POST['optionval'.$i.'-'.$j]).'|||' : '');
						}
					}
					# Check to make sure at least ONE option was in fact added
					if($optionVals != "") {
						$q .= "('". $info['sessiontype_id'] ."','". $info['producttype_id'] ."','". (isset($_POST['required'.$i]) && $_POST['required'.$i] == "on" ? 1 : 0) ."','". $_POST['fieldtype_id'.$i] ."','". $_POST['sort_order'.$i] ."','". encode($_POST['question'.$i]) ."','". substr($optionVals,0,-3) ."','".$_SESSION['show_id']."'),";	
					}
				}
			}
		}
	}
	
	if($q != "") {
		db_query("INSERT INTO pt_questions (sessiontype_id,producttype_id,required,fieldtype_id,sort_order,question,options,show_id) VALUES ".substr($q,0,-1),"Adding the questions");
	}

	
	$_SESSION['infoMessages'][] = "Questions has been successfully updated in the Database.";
	header("Location: index.php");
	die();		
?>