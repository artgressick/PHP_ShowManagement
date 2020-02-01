<?php
function form_text($args) {
	
	####################################################################
	## function form_text Example:
	## form_text(array(
	##			 'caption' => 'Field Caption',					// Caption Displayed Above Field
	##			 'nocaption' => 'Do Not Display Caption',		// Suppresses Caption and Required text
	##			 'type' => 'text OR password',					// Default is text, For Password Field use password
	##			 'display' => 'true',							// Shows the Value, Great for a view page.							
	##			 'title' => 'Roll Over Title',					// Displays this text when mouse is hovered over field 
	##			 'value' => encode(Field Value),				// Value of the field, be sure to encode this
	##			 'name' => 'Field Name',						// Name of field for Post, (ie. chrFirst)
	##			 'id' => 'Field ID',							// ID of field for JS, (ie. chrFirst)
	##			 'required' => 'true',							// true = Indicates field being required, any other value shows that value in the required format and in parenthesis
	##			 'size' => 'Field Size',						// Total Size of Box
	##			 'maxlength' => 'Max Field Length',				// Max Characters that can be entered
	##			 'class' => 'Field Class',						// Class of Text Field
	##			 'style' => 'CSS Style',						// Extra Styles for Text Box
	##			 'extra' => 'Extra Code, Javascript, etc',		// For additional JS, to Disable Field, or any additional options can be entered here.
	##			));
	####################################################################
	
	if(is_array($args)) { 
			
		$name = (isset($args['name']) ? $args['name'] : '');
		$caption = (isset($args['caption']) ? $args['caption'] : $name);
		$title = (isset($args['title']) ? $args['title'] : $caption);
		$value = (isset($args['value']) ? $args['value'] : '');
		$required = (isset($args['required']) && $args['required']=='true' ? " <span class='FormRequired'>(Required)</span>" : (isset($args['required']) ? " <span class='FormRequired'>(".$args['required'].")</span>" : ''));
		$id = (isset($args['id']) ? $args['id'] : $name);
		$size = (isset($args['size']) ? $args['size'] : '');
		$maxlength = (isset($args['maxlength']) ? $args['maxlength'] : '');
		
		$class = (isset($args['class']) ? $args['class'] : '');
		$style = (isset($args['style']) ? $args['style'] : '');
		
		$extra = (isset($args['extra']) ? $args['extra'] : '');
		$tmp = "";
		$wrapper = "span";
		if(!isset($args['nocaption'])) {
			$tmp .= "<div class='FormName'>".$caption . $required."</div>\n";
			$wrapper = "div";
		}
		
		if(isset($args['display'])) { 
			return $tmp."<div class='FormDisplay'".($style != '' ? " style='".$style."'" : "").">".$value."</div>";
		} else {
			return $tmp."<".$wrapper." class='FormField'><input type='".(isset($args['type'])?$args['type']:'text')."' name='".$name."' id='".$id."'".($size!=''? " size='".$size."'" :'').($maxlength!=''? " maxlength='".$maxlength."'" :'').($title!=''? " title='".$title."'" :'').($value!=''? " value='".$value."'" :'').($class!=''? " class='".$class."'" :'').($style!=''? " style='".$style."'" :'').($extra!=''? " ".$extra :'')." /></".$wrapper.">";
		}
		
		if($name == "") { 
			return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("Text Field name missing"); }</script>';
		}
	} else {
		return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("No Arguments were supplied to the Text Field"); }</script>';
	}
}

function form_select($records,$args) {
	####################################################################
	## function form_select Example:
	## form_select(
	##			 $DataSet,										// Array of Values or Query Results. MUST BE FIRST. 
	##															// 		For Query - fields need to be ID && chrRecord. 
	##			 array(											//		For Array - setup Array with a Key and Value (array('key1' => 'value1','key2' => 'value2') etc...)
	##			 'type' => 'simplearray',						// ONLY FOR ARRAY $DataSet, is this a simplerray (KEY and VALUE Same), standard (DEFAULT, KEY and VALUE Different) or grouparray (Standard array with third optgroup value) 
	##			 'caption' => 'Field Caption',					// Caption Displayed Above Field
	##			 'nocaption' => 'Do Not Display Caption'		// Suppresses Caption and Required text
	##			 'display' => 'true',							// Shows the Value, Great for a view page.
	##			 'title' => 'Roll Over Title',					// Displays this text when mouse is hovered over field 
	##			 'value' => encode(Field Value),				// Value of the field, be sure to encode this
	##			 'name' => 'Field Name',						// Name of field for Post, (ie. chrFirst)
	##			 'id' => 'Field ID',							// ID of field for JS, (ie. chrFirst)
	##			 'required' => 'true',							// true = Indicates field being required, any other value shows that value in the required format and in parenthesis
	##			 'class' => 'Field Class',						// Class of Text Field
	##			 'style' => 'CSS Style',						// Extra Styles for Text Box
	##			 'extra' => 'Extra Code, Javascript, etc',		// For additional JS, to Disable Field, or any additional options can be entered here.
	##			));
	####################################################################

	if(is_array($args)) { 

		$name = (isset($args['name']) ? $args['name'] : '');
		$caption = (isset($args['caption']) ? $args['caption'] : $name);
		$title = (isset($args['title']) ? $args['title'] : $caption);
		$value = (isset($args['value']) ? $args['value'] : '');
		$required = (isset($args['required']) && $args['required']=='true' ? " <span class='FormRequired'>(Required)</span>" : (isset($args['required']) ? " <span class='FormRequired'>(".$args['required'].")</span>" : ''));
		$id = (isset($args['id']) ? $args['id'] : $name);
		
		$class = (isset($args['class']) ? $args['class'] : '');
		$style = (isset($args['style']) ? $args['style'] : '');
		
		$extra = (isset($args['extra']) ? $args['extra'] : '');
		$tmp = '';
		if(!isset($args['nocaption'])) {
			$tmp .= "<div class='FormName'>".$caption . $required."</div>";
		}
		
		if(isset($args['display'])) { 
			if ($value != '') {
				if(!is_array($records)) {
					while($row = mysqli_fetch_assoc($records)) {
						if($row['id'] == $value) {
							$tmp .= "<div class='FormDisplay'>".$row['name']."</div>";
							break;
						}
					}
				} else { 
					if(isset($args['simplearray'])) {
						$tmp .= "<div class='FormDisplay'>".$value."</div>";
					} else {
						while ($array_value = current($records)) {
							if(key($records) == $value) {
							$tmp .= "<div class='FormDisplay'>".$array_value."</div>";
								break;
							}
							next($records);
						}
					}
				}
			} else {
				$tmp .= "<div class='FormDisplay'>N/A</div>";
			}	
			return $tmp;
		} else {
			$tmp .= (isset($args['nocaption']) ? '' : '<div class="FormField">')."<select id='".$id."' name='".$name."' ".($title!=''? " title='".$title."'" :'').($class!=''? " class='".$class."'" :'').($style!=''? " style='".$style."'" :'').($extra!=''? " ".$extra :'').">";
			if((($value == '' && isset($args['required'])) || !isset($args['required'])) && !isset($args['firstoption'])) {
				$tmp .= "<option value=''>".(!isset($args['nocaption']) ? '- Select '.$caption.' -' : $caption)."</option>";
			} else if (isset($args['firstoption'])) {
				$tmp .= "<option value=''>".$args['firstoption']."</option>";
			}
			$opt_group = "";
			if(!is_array($records)) {
				while($row = mysqli_fetch_assoc($records)) {
					if(isset($row['optGroup']) && $opt_group != $row['optGroup']) {
						if($opt_group != "") { $tmp .= "</optgroup>"; }
						$opt_group = $row['optGroup'];
						$tmp .= "<optgroup label='".$row['optGroup']."'>";
					}
					if(isset($row['lkey'])) {
						$tmp .= "<option".($value != '' && $row['lkey'] == $value ? ' selected="selected"' : '')." value='".$row['lkey']."'>".decode($row['name'])."</option>";
					} else {
						$tmp .= "<option".($value != '' && $row['id'] == $value ? ' selected="selected"' : '')." value='".$row['id']."'>".decode($row['name'])."</option>";
					}
				}

			} else { 
				if(isset($args['type']) && $args['type'] == 'simplearray') {
					while ($array_value = current($records)) {
						$tmp .= "<option".($value != '' && $array_value == $value ? ' selected="selected"' : '')." value='".$array_value."'>".decode($array_value)."</option>";		
						next($records);
					}
				} else if(isset($args['type']) && $args['type'] == 'grouparray') {
					while ($array_value = current($records)) {
						if($opt_group != $array_value['optGroup']) {
							if($opt_group != "") { $tmp .= "</optgroup>"; }
							$opt_group = $array_value['optGroup'];
							$tmp .= "<optgroup label='".$array_value['optGroup']."'>";
						}
						$tmp .= "<option".($value != '' && $array_value['value'] == $value ? ' selected="selected"' : '')." value='".$array_value['value']."'>".decode($array_value['display'])."</option>";		
						next($records);
					}
				} else {
					while ($array_value = current($records)) {
						$tmp .= "<option".($value != '' && key($records) == $value ? ' selected="selected"' : '')." value='".key($records)."'>".decode($array_value)."</option>";		
						next($records);
					}
				}
			}
			$tmp .=	"</select>".(isset($args['nocaption']) ? '' : '</div>');
			return $tmp;
		}

		if($name == "") { 
			return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("Select Field name missing"); }</script>';
		}
	} else {
		return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("No Arguments were supplied to the Select Field"); }</script>';
	}
}

function form_checkbox($args) {
	
	####################################################################
	## function form_checkbox Example:
	## form_checkbox(array(
	##			 'type' => 'Checkbox/Radio',					// Choose which one you are using -- Checkbox by default
	##			 'caption' => 'Field Caption',					// Caption Displayed Above Field
	##			 'title' => 'Roll Over Title',					// Displays this text when mouse is hovered over field 
	##			 'array' => 'true',								// Checkboxes will be considered arrays with the same name
	##			 'value' => encode(Field Value),				// Value of the field, be sure to encode this
	##			 'name' => 'Field Name',						// Name of field for Post, (ie. chrFirst)
	##			 'id' => 'Field ID',							// ID of field for JS, (ie. chrFirst)
	##			 'checked' => 'true OR false',					// Indicates where to check this box or not, Default false
	##			 'required' => 'Required T/F',					// Shows or Suppresses the Required Text next to the Caption, isset check
	##			 'class' => 'Field Class',						// Class of Text Field
	##			 'style' => 'CSS Style',						// Extra Styles for Text Box
	##			 'extra' => 'Extra Code, Javascript, etc',		// For additional JS, to Disable Field, or any additional options can be entered here.
	##			));
	####################################################################

	
	if(is_array($args)) { 
			
		$type = (isset($args['type']) ? $args['type'] : 'checkbox');
		$name = (isset($args['name']) ? $args['name'] : '');
		$caption = (isset($args['caption']) ? $args['caption'] : $name);
		$title = (isset($args['title']) ? $args['title'] : $caption);
		$value = (isset($args['value']) ? $args['value'] : '');
		$required = (isset($args['required']) && $args['required']=='true' ? " <span class='FormRequired'>(Required)</span>" : (isset($args['required']) ? " <span class='FormRequired'>(".$args['required'].")</span>" : ''));
		$id = (isset($args['id']) ? $args['id'] : str_replace('[]','',$name).$value);
		
		$class = (isset($args['class']) ? $args['class'] : '');
		$style = (isset($args['style']) ? $args['style'] : '');
		
		$extra = (isset($args['extra']) ? $args['extra'] : '');
		$tmp = '';
		if(isset($args['caption'])) {
			$tmp .= "<div class='FormName'>".$caption . $required."</div>";
		}
		$tmp .= "<span><input type='".$type."' name='".$name.(isset($args['array']) && $type != 'radio' ? '[]' : '')."' id='".$id."'".(isset($args['checked']) && $args['checked'] == 'true' ? ' checked="checked"':'').($title!=''? " title='".$title."'" :'').($value!=''? " value='".$value."'" :'').($class!=''? " class='".$class."'" :'').($style!=''? " style='".$style."'" :'').($extra!=''? " ".$extra :'')." /> <label for='".$id."'>".($caption != "" ? addslashes($title) : '')."</label></span>";
		return $tmp;
		if($name == "") { 
			return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("Checkbox Field name missing"); }</script>';
		}
	} else {
		return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("No Arguments were supplied to the Checkbox Field"); }</script>';
	}
}

function form_textarea($args) {
	
	####################################################################
	## function form_text Example:
	## form_text(array(
	##			 'caption' => 'Field Caption',					// Caption Displayed Above Field
	##			 'title' => 'Roll Over Title',					// Displays this text when mouse is hovered over field 
	##			 'value' => Field Value,				// Value of the field, be sure to encode this
	##			 'name' => 'Field Name',						// Name of field for Post, (ie. chrFirst)
	##			 'nocaption' => 'Do Not Display Caption'		// Suppresses Caption and Required text
	##			 'id' => 'Field ID',							// ID of field for JS, (ie. chrFirst)
	##			 'required' => 'true',							// true = Indicates field being required, any other value shows that value in the required format and in parenthesis
	##			 'rows' => 'Field Size',						// Total Size of Box
	##			 'cols' => 'Max Field Length',				// Max Characters that can be entered
	##			 'class' => 'Field Class',						// Class of Text Field
	##			 'style' => 'CSS Style',						// Extra Styles for Text Box
	##			 'extra' => 'Extra Code, Javascript, etc',		// For additional JS, to Disable Field, or any additional options can be entered here.
	##			));
	####################################################################
	
	if(is_array($args)) { 
			
		$name = (isset($args['name']) ? $args['name'] : '');
		$caption = (isset($args['caption']) ? $args['caption'] : $name);
		$title = (isset($args['title']) ? $args['title'] : $caption);
		$value = (isset($args['value']) ? $args['value'] : '');
		$required = (isset($args['required']) && $args['required']=='true' ? " <span class='FormRequired'>(Required)</span>" : (isset($args['required']) ? " <span class='FormRequired'>(".$args['required'].")</span>" : ''));
		$id = (isset($args['id']) ? $args['id'] : $name);
		$rows = (isset($args['rows']) ? $args['rows'] : '');
		$cols = (isset($args['cols']) ? $args['cols'] : '');
		
		$class = (isset($args['class']) ? $args['class'] : '');
		$style = (isset($args['style']) ? $args['style'] : '');
		
		$extra = (isset($args['extra']) ? $args['extra'] : '');
		$tmp = '';
		if(!isset($args['nocaption'])) {
			$tmp .= "<div class='FormName'>".$caption . $required."</div>";
		}
		$tmp .= "<textarea name='".$name."' id='".$id."'".($rows!=''? " rows='".$rows."'" :'').($cols!=''? " cols='".$cols."'" :'').($title!=''? " title='".$title."'" :'').($class!=''? " class='".$class."'" :'').($style!=''? " style='".$style."'" :'').($extra!=''? " ".$extra :'').">".$value."</textarea>";
		return $tmp;
		if($name == "") { 
			return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("Textarea name missing"); }</script>';
		}
	} else {
		return '<script type="text/javascript">var name=\''.$name.'\';if(name==\'\') { alert("No Arguments were supplied to the Textarea"); }</script>';
	}
}
?>