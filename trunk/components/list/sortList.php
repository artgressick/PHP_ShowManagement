<?
	#	SortList created for PHP to set up a sortable column table on the server side

if(!isset($BF)) { $BF = ""; }
if(!isset($_REQUEST['ordCol'])) { $_REQUEST['ordCol'] = "ASC"; }

# The main sortlist that takes a hash of values as well as the optional values and 
function sortList($hash, $style='', $morecgi='', $params='') {
	global $BF;
	$multi = "";
	$tmp = array();

	if(preg_match('/,/', $_REQUEST['sortCol'])) { 
		$multi = $_REQUEST['sortCol'];
		$tmp = split(',',$_REQUEST['sortCol']);	
	}

	foreach($hash as $label => $column_name) {
		if(preg_match('/^opt_(\w)*/', $label) {
			
			?><th class='options'><?=$column_name?><?
			
		} else {
			# If there label does NOT start with the opt_, then add it normally
			if(($_REQUEST['sortCol'] == $column_name) || $tmp[0] == $column_name) {
				if($_REQUEST['ordCol'] == 'ASC') {
					$link = '?sortCol=' . $column_name . '&ordCol=DESC' . ($morecgi!=''?'&amp;' . $morecgi:'');	
					$graphic = 'column_sorted_asc.gif';
					$image_alt = 'Ascending order image';
					$link_title = 'Sort by ' . $label . ' in ascending order';
				} else {
					$link = '?sortCol=' . $column_name . '&ordCol=ASC' . ($morecgi!=''?'&amp;' . $morecgi:'');	
					$graphic = 'column_sorted_desc.gif';
					$image_alt = 'Descending order image';
					$link_title = 'Sort by ' . $label . ' in descending order';			
				}
			} else {
				$link = '?sortCol=' . $column_name . '&ordCol=ASC' . ($morecgi!=''?'&amp;' . $morecgi:'');
				$graphic = '';
				$image_alt = 'Currently not sorted by this column';
				$link_title = 'Sort by ' . $label . ' in ascending order';
			}
		
?><th style='<?=$style?>' onclick='location.href="<?=$_SERVER['PHP_SELF']?><?=$link?>";' class='<?=(($_REQUEST['sortCol'] == $column_name) || ($tmp[0] == $column_name) ? 'ListHeadSortOn' : '')?>' <?=$params?>>
									<a class='<?$_REQUEST['sortCol']==$column_name ? 'current' : ''?>' title='<?=$link_title?>' href='<?=$_SERVER['PHP_SELF']?><?=$link?>'><?=$label?></a>&nbsp;
				
			<? if($graphic!='') { ?><img src='<?=$BF?>components/list/<?=$graphic?>' alt='<?=$image_alt?>' style='padding-top: 2px;' /><? } ?>
			</th>
<?
		}
	}
}

# Delete buttons for the list pages in the options section
function deleteButton($id,$message,$chrKEY) {
	global $BF;
	?>
		<span class='deleteImage' onmouseover='document.getElementById("deleteButton<?=$id?>").src="<?=$BF?>images/button_delete_on.png"' onmouseout='document.getElementById("deleteButton<?=$id?>").src="<?=$BF?>images/button_delete.png"'><a href="javascript:warning(<?=$id?>, '<?=str_replace("&","&amp;",$message)?>','<?=$chrKEY?>');"><img id='deleteButton<?=$id?>' src='<?=$BF?>images/button_delete.png' alt='delete button' /></a></span>
	<?
}


/* This is the Listing section, all CSS that affect Listing pages go in the area.*/
?>
<style type='text/css'>

.List { border: 1px solid #999; padding: 0; margin: 0; }
.List th { font-size: 10px; background: url(<?=$BF?>components/list/list_head.gif) repeat-x; height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; text-align: left; white-space: nowrap; }
.List td { padding: 0 5px; font-size: 11px; cursor: pointer; }
.List th a { color: #333; text-decoration: none; }
.List td a { color: black; text-decoration: none; }
.List th.ListHeadSortOn { font-size: 10px; background: url(<?=$BF?>components/list/list_head_sortedby.gif); height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; }
.List .ListLineOdd { font-size: 10px; background-color: #FFF; line-height: 20px; height: 20px; padding-left: 5px; }
.List .ListLineEven { font-size: 10px; background-color: #EEE; line-height: 20px; height: 20px; padding-left: 5px; }
.List .options { width: 0.1in; white-space: nowrap; text-align: right; } 
.List .options a { text-decoration: underline; color: green; } 

.List .options a { text-decoration: underline; color: green; } 
</style>
<?

# This is the Listing section, all Javascript that affect Listing pages go in the area.

?>
<script type='text/javascript'>
var highlightTmp = "";
function RowHighlight(row) {
	highlightTmp = (document.getElementById(row).style.backgroundColor != "" ? document.getElementById(row).style.backgroundColor : '');
	document.getElementById(row).style.backgroundColor = '#AFCCFF';
}
function UnRowHighlight(row) {
	document.getElementById(row).style.backgroundColor = (highlightTmp == '' ? '' : highlightTmp);
}
// This function re-paints the list tables
function repaint() {
	var menuitems = document.getElementById('List').getElementsByTagName("tr");
	var j = 0;
	for (var i=1; i<menuitems.length; i++) {
		if(menuitems[i].style.display != "none") {
			((j%2) == 0 ? menuitems[i].className = "ListLineEven" : menuitems[i].className = "ListLineOdd");
			j += 1;
		}		
	}
}
</script>
