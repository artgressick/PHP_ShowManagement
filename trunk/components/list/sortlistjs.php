<?	
	function sortList($table, $hash, $query, $linkto, $style='', $db_table='') { 
		global $BF;
		if($db_table=='') { $db_table = $table; }
?>
	<table id='<?=$table?>' class='List sortable' style='<?=(isset($style) ? $style : 'width: 100%;')?>' cellpadding="0" cellspacing="0">
		<thead>
		<tr>
<?	foreach($hash as $k => $v) { 
		if(is_array($v) && !preg_match('/^opt_/',$k)) { 
			if(isset($hash[$k]['default'])) { 
				$sortDir = strtolower($hash[$k]['default']);
?>			<th class='ListHeadSortOn <?=($sortDir != 'desc' ? 'sorttable_sorted' : 'sorttable_sorted_reverse')?><?=(isset($hash[$k]['sorttype']) ? ' sorttable_'. $hash[$k]['sorttype'] : '')?>' <?=(isset($hash[$k]['style']) ? ' style="'. $hash[$k]['style'] .'"' : '')?>><?=$hash[$k]['displayName']?>&nbsp;<img src='<?=$BF?>components/list/column_sorted_<?=($sortDir != 'desc' ? 'asc' : 'desc')?>.gif' alt='sorted' style='vertical-align: bottom;' /><span id='<?=($sortDir != 'desc' ? 'sorttable_sortfwdind' : 'sorttable_sortrevind')?>'></span></th>
<?			} else {
?>			<th<?=(isset($hash[$k]['sorttype']) ? ' class="sorttable_'. $hash[$k]['sorttype'] .'"' : '')?><?=(isset($hash[$k]['style']) ? ' style="'. $hash[$k]['style'] .'"' : '')?>><?=$hash[$k]['displayName']?>&nbsp;<img src='<?=$BF?>components/list/column_unsorted.gif' alt='default sort' style='vertical-align: bottom;' /></th>
<?			}
		} else { 
			if(preg_match('/^opt_other/',$k) && $v == 'checkboxes') {
?>			<th class="options sorttable_nosort"><input type=checkbox name="chkbutton" id="chkbutton" title="Check all" onClick="togglecheckboxes()"></th>
<?
			} else if ($v == 'quantity') {
?>			<th<?=(preg_match('/^opt_/',$k) ? ' class="options sorttable_nosort"' : '')?>>Qty</th>
<?			} else {
?>			<th<?=(preg_match('/^opt_/',$k) ? ' class="options sorttable_nosort"' : '')?>><img src='<?=$BF?>images/options.gif' alt='options' /></th>
<?			}
		}
	} ?>
		</tr>
		</thead>
		<tbody>
<?		$count = 0;
		if(mysqli_num_rows($query)) { 
			$linktype = (preg_match('/(\?|\&)key\=/',$linkto) ? 'lkey' : 'id');
			
			while($row = mysqli_fetch_assoc($query)) { 
			if(!isset($row['lkey'])) { $row['lkey'] = ""; }
			$extra_css = "";
			if($table == 'sessioncheckin') {
				$start_dt = strtotime($row['start_date'].' '.$row['start_time']);
				$now = strtotime('Now');
				if($row['checked_datetime'] != '') {
					$extra_css = " style='background:#92ff92;'";
				} else if($now >= ($start_dt - 900) && $now < ($start_dt - 300)) {
					$extra_css = " style='background:#f9ff5f;'";
				} else if($now >= ($start_dt - 300)) {
					$extra_css = " style='background:#ff9696;'";
				}
			} else if($table == 'roomschecked') {
				if($row['checked_datetime'] != '') {
					$extra_css = " style='background:#92ff92;'";
				}
			}
			
			
?>			<tr id='<?=$table?>tr<?=$row['id'].$count?>' class='<?=($count%2 ? 'ListOdd' : 'ListEven')?>' 
			onmouseover='RowHighlight("<?=$table?>tr<?=$row['id'].$count?>");' onmouseout='UnRowHighlight("<?=$table?>tr<?=$row['id'].$count?>");'<?=$extra_css?>>
<?	foreach($hash as $k => $v) { 
		if(isset($hash[$k]['format'])) {
			if($hash[$k]['format'] == "date_time" && $row[$k] != "") { $row[$k] = '<span style="display:none;">'.$row[$k].'</span>'.pretty_datetime($row[$k]); }
			if($hash[$k]['format'] == "date_time2" && $row[$k] != "") { $row[$k] = '<span style="display:none;">'.$row[$k].'</span>'.pretty_datetime2($row[$k]); }
			if($hash[$k]['format'] == "date" && $row[$k] != "") { $row[$k] = '<span style="display:none;">'.$row[$k].'</span>'.pretty_date($row[$k]); }
			if($hash[$k]['format'] == "time" && $row[$k] != "") { $row[$k] = '<span style="display:none;">'.$row[$k].'</span>'.pretty_time($row[$k]); }
		}


		if(is_array($v) && !preg_match('/^opt_/',$k)) {
			if($linkto != '') { 
?>			<td<?=(isset($hash[$k]['rowstyle']) ? ' style="'. $hash[$k]['rowstyle'] .'"' : '')?> onclick='window.location.href="<?=$linkto?><?=$row[$linktype]?>"'><?=$row[$k]?></td>
<?			} else {
?>			<td<?=(isset($hash[$k]['rowstyle']) ? ' style="'. $hash[$k]['rowstyle'] .'"' : '')?> class='nocursor'><?=$row[$k]?></td>
<?			}			
		} else { 
			if(preg_match('/^opt_del$/',$k)) {
				if(preg_match('/,/',$v)) { 
					$tmpVal = explode(',',$v);
					$displayVal = "";
					foreach($tmpVal as $val) { $displayVal .= $row[$val]." "; }
					$displayVal = substr($displayVal,0,-1);
				} else {
					$displayVal = $row[$v];
				}
				?>			<td class='options'><? deleteButton($row['id'],$displayVal,$row['lkey'],$table,$count); ?></td> 		<?
			} else if (preg_match('/^opt_other$/',$k)) { 
				if ($v == 'order') {
?>					<td><?=orderBoxes($row['id'],$row['order'])?></td>
<?
				} else if($v == 'checkboxes') {
?>					<td><input type='checkbox' name='listids[]' id='<?=$table?>id<?=$row['id']?>' value='<?=$row['id']?>' onClick="multiselect(event,'<?=$table?>id<?=$row['id']?>');"/></td>
<?
				} else if ($v == 'quantity') {
					if($row['needs_quantity']) {
?>					<td><?=quantityBoxes($row['id'],$row['quantity'],$db_table)?></td>
<?
					} else {
?>					<td>N/A</td>
<?
					}
				} else if ($v == 'reset_room') {
?>					<td><? if($row['checked_datetime'] != "") { ?><input type='button' onclick="javascript:document.getElementById('idReset').value='<?=$row['id']?>';document.getElementById('idForm').submit();" value='Reset' /><? } else { ?><input type='button' onclick="javascript:document.getElementById('rcheckoff').value='<?=$row['id']?>';document.getElementById('idForm').submit();" value='Check In Room' /><? } ?></td>
<?
				} else if ($v == 'reset_session') {
?>					<td><? if($row['checked_datetime'] != '') { ?><input type='button' onclick="javascript:document.getElementById('idReset').value='<?=$row['id']?>';document.getElementById('idForm').submit();" value='Reset' /><? } else { ?><input type='button' onclick="javascript:document.getElementById('scheckoff').value='<?=$row['id']?>';document.getElementById('idForm').submit();" value='Check In Session' /><? } ?></td>
<?
				}
				
			} else if (preg_match('/^opt_link$/',$k)) { 
				$v['address'].=(preg_match('/(\?|\&)key\=/',$linkto) ? $row['lkey'] : $row['id']);
?>				<td><?=linkto($v)?></td>
<?	

			} else { 
?>				<td><?=$v?></td>
<?			}
		}
	} 
?>		
			</tr>
<?
			$count++;
			}
		} else {
?>
			<tr>
				<td colspan='<?=count($hash)?>' style='text-align:center;height:20px;vertical-align:middle;'>No records found in the database.</td>
			</tr> 	
<?		} ?>
		</tbody>
	</table>

<?	} 


function deleteButton($id,$message,$chrKEY,$table,$count) {
	global $BF;
	?><span class='deleteImage'><a href="javascript:warning(<?=$id?>, '<?=str_replace("&","&amp;",$message)?>','<?=$chrKEY?>','<?=$table?>','<?=$count?>');" title="Delete: <?=$message?>"><img id='deleteButton<?=$id?>' src='<?=$BF?>images/button_delete.png' alt='delete button' onmouseover='this.src="<?=$BF?>images/button_delete_on.png"' onmouseout='this.src="<?=$BF?>images/button_delete.png"' /></a></span><?
}

function orderBoxes($id,$value) {
	?><input type="text" size="3" name="intOrder<?=$id?>" id="intOrder<?=$id?>" value="<?=$value?>" /><?
}
function quantityBoxes($id,$value,$db_table) {
	global $BF;
	?><input type="text" size="3" id="quantity<?=$id?>" value="<?=$value?>" onkeyup="javascript:update_quantity('<?=$BF?>',<?=$id?>,'<?=$db_table?>', this.value);" /><?
}

global $BF;
?>


	<style type='text/css'>
	
		.List { border: 1px solid #999; padding: 0; margin: 0; }
		.List th { font-size: 10px; background: url(<?=$BF?>components/list/list_head.gif) repeat-x; height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; text-align: left; white-space: nowrap; }
		.List td { padding: 0 5px; font-size: 11px; cursor: pointer; }
		.List .nocursor { cursor:auto; }
		.List th a { color: #333; text-decoration: none; }
		.List td a { color: black; text-decoration: none; }
		.List th.ListHeadSortOn { font-size: 10px; background: url(<?=$BF?>components/list/list_head_sortedby.gif); height: 13px; border-bottom: 1px solid #999; padding: 3px 5px; font-weight: bold; }
		.List .ListOdd { font-size: 10px; background-color: #FFF; line-height: 20px; height: 20px; padding-left: 5px; }
		.List .ListEven { font-size: 10px; background-color: #EEE; line-height: 20px; height: 20px; padding-left: 5px; }
		.List .options { width: 10px; white-space: nowrap; text-align: center; vertical-align:middle; } 
		.List .options a { text-decoration: underline; color: green; } 
		
		.List .options a { text-decoration: underline; color: green; } 
	</style>
<?

# This is the Listing section, all Javascript that affect Listing pages go in the area.

?>
	<script type='text/javascript' src='<?=$BF?>components/list/_sorttable.js'></script>
	<script type="text/javascript">
		var checkflag = false;
		function init(){
			document.onkeydown = register;
			document.onkeyup = register;
			document.onclick = register;
			if (document.body.scrollTop == 0)
			document.searchform.search.focus();
		}

		function register(e){
			if (!e) e = window.event;
			var skey = 'shiftKey';
			var ckey = 'crtlKey';
			shiftpressed = e[skey];
			controlpressed = e[ckey];
		}
		function multiselect(e,v) {
			if(!e)e=window.event;
			var skey='shiftKey';
			var ckey='ctrlKey';
			shiftpressed = e[skey];
			controlpressed = e[ckey];
			if(shiftpressed == false) {
				firstselected = v;
				if(controlpressed == false) {
				} else {
					chk = document.getElementsByTagName('input');
					for(i=0;i<chk.length;i++) {
						if(chk[i].name.indexOf('listids')>-1) {
							if(chk[i].id != v) {
								chk[i].checked = false;
							}
						}
					}
				}
			} else {
				lastselected = v;
				start = false;
				chk = document.getElementsByTagName('input');
				for(i=0;i<chk.length;i++) {
					if(chk[i].name.indexOf('listids')>-1) {
						if(start == false && chk[i].id == firstselected) {
							start = true;
						}
						if(start == true) {
							chk[i].checked = true;
						}
						if(chk[i].id == lastselected){
							break;
						}
					}
				}
			}
		}
		function togglecheckboxes() {
			if(checkflag == false){
				val=true;
				checkflag=true;
				title="Uncheck All";
			} else {
				val=false;
				checkflag=false;
				title="Check All";
			}
			chk = document.getElementsByTagName('input');
				for(i=0;i<chk.length;i++){
					if(chk[i].name.indexOf('listids')>-1) {
						chk[i].checked = val;
					}
				}
			document.getElementById('chkbutton').title = title;
		}
	
	var highlightTmp = "";
		function RowHighlight(row) {
			highlightTmp = (document.getElementById(row).style.backgroundColor != "" ? document.getElementById(row).style.backgroundColor : '');
			document.getElementById(row).style.backgroundColor = '#AFCCFF';
		}
		function UnRowHighlight(row) {
			document.getElementById(row).style.backgroundColor = (highlightTmp == '' ? '' : highlightTmp);
		}
		// This function re-paints the list tables
		function repaint(tblName) {
			var menuitems = document.getElementById(tblName).getElementsByTagName("tr");
			var j = 0;
			var menulen = menuitems.length;
			for (var i=1; i < menulen; i++) {
				if(menuitems[i].style.display != "none") {
					((j%2) == 0 ? menuitems[i].className = "ListEven" : menuitems[i].className = "ListOdd");
					j += 1;
				}		
			}
		}
	</script>
