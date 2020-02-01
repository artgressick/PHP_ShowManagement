<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="width:50%; vertical-align:top;">

				<div class="colHeader">Product Type Information</div>

				<?=form_text(array('caption'=>'Product Type Name','required'=>'true','name'=>'product_type','size'=>'30','maxlength'=>'150','value'=>$info['product_type']))?>
				
				<?=form_checkbox(array('type'=>'radio','caption'=>'Enable Product Type','title'=>'No','name'=>'enabled','id'=>'enabled0','value'=>'0','required'=>'true','checked'=>(!$info['enabled']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

				<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'enabled','id'=>'enabled1','value'=>'1','checked'=>($info['enabled']?'true':'false')))?>
				
				<?=form_textarea(array('caption'=>'Request Note','required'=>'Disclaimer at top of section','name'=>'request_note','cols'=>'50','rows'=>'5','value'=>$info['request_note']))?>

				<?=form_textarea(array('caption'=>'Top Custom Request Instructions','required'=>'Displays Above Custom Order Box','name'=>'custom_top_instructions','cols'=>'50','rows'=>'5','value'=>$info['custom_top_instructions']))?>
				
				<?=form_textarea(array('caption'=>'Custom Request Instructions','required'=>'Displays under Custom Order Box','name'=>'custom_instructions','cols'=>'50','rows'=>'5','value'=>$info['custom_instructions']))?>
				
					</td>
					<td style="width:50%; vertical-align:top;">
				
				<div class="colHeader">Session Types Restrictions</div>
				<div style="font-size:10px; font-weight:bold;">(This will over product-session type settings)</div>
				<table cellpadding="0" cellspacing="10" width="100%">
					<tr>
						<td colspan="2"><input type="button" value="Check All" onclick="checkall('allow');" /> <input type="button" value="UnCheck All" onclick="uncheckall('allow');" /></td>
						
						<td colspan="2"><input type="button" value="Check All" onclick="checkall('custom');" /> <input type="button" value="UnCheck All" onclick="uncheckall('custom');" /></td>
						
					</tr>

<?
				$temp = db_query("SELECT * FROM sessiontype_producttypes WHERE producttype_id=".$info['id'],"Getting Session Type Data");
				while($row = mysqli_fetch_assoc($temp)) {
					$enabled[$row['sessiontype_id']] = $row['allowcustom'];
				}
	
				$sessiontypes = db_query("SELECT * FROM sessiontypes WHERE !deleted ORDER BY sessiontype_name","Getting Session Type Names");
				$ids = "";
				while($row = mysqli_fetch_assoc($sessiontypes)) {
					$ids .= $row['id'].',';
?>
					<tr>
						<td style="width:10px;"><input type="checkbox" name="allow<?=$row['id']?>" id="allow<?=$row['id']?>" onchange="check(<?=$row['id']?>);" <?=(isset($enabled[$row['id']]) ? ' checked="checked"':'')?> /></td>
						<td><label for='allow<?=$row['id']?>'><?=$row['sessiontype_name']?></label></td>
						<td style="width:10px;"><input type="checkbox" name="custom<?=$row['id']?>" id="custom<?=$row['id']?>" <?=(isset($enabled[$row['id']]) ? '':' disabled="disabled"')?><?=(isset($enabled[$row['id']]) && $enabled[$row['id']] == 1 ? ' checked="checked"':'')?> onchange="markchange();" /></td>
						<td><label for='custom<?=$row['id']?>'>Allow Custom Orders</label></td>
					</tr>
<?
				}
?>
				</table>
					<input type="hidden" name="typechanged" id="typechanged" value="0" />
					<input type="hidden" name="ids" id="ids" value="<?=substr($ids,0,-1)?>" />
					</td>
				</tr>
			</table>


			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Update Information'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
			</div>
		</form>
	</div>

<?
	}
?>