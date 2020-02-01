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

				<div class="colHeader">Session Type Information</div>

				<?=form_text(array('caption'=>'Session Type Name','required'=>'true','name'=>'sessiontype_name','size'=>'30','maxlength'=>'150','value'=>$info['sessiontype_name']))?>
				
				<?=form_checkbox(array('type'=>'radio','caption'=>'Ignore Lock Date','title'=>'No','name'=>'ignore_lock_date','id'=>'ignore_lock_date0','value'=>'0','required'=>'true','checked'=>(!$info['ignore_lock_date']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'ignore_lock_date','id'=>'ignore_lock_date1','value'=>'1','checked'=>($info['ignore_lock_date']?'true':'false')))?>
				
					</td>
					<td style="width:50%; vertical-align:top;">
				
				<div class="colHeader">Product Types Restrictions</div>
				<div style="font-size:10px; font-weight:bold;">(This will over product-session type settings)</div>
				<table cellpadding="0" cellspacing="10" width="100%">
					<tr>
						<td colspan="2"><input type="button" value="Check All" onclick="checkall('allow');" /> <input type="button" value="UnCheck All" onclick="uncheckall('allow');" /></td>
						
						<td colspan="2"><input type="button" value="Check All" onclick="checkall('custom');" /> <input type="button" value="UnCheck All" onclick="uncheckall('custom');" /></td>
						
					</tr>

<?
				$temp = db_query("SELECT * FROM sessiontype_producttypes WHERE sessiontype_id=".$info['id'],"Getting Product Type Data");
				while($row = mysqli_fetch_assoc($temp)) {
					$enabled[$row['producttype_id']] = $row['allowcustom'];
				}
	
				$producttypes = db_query("SELECT * FROM product_types WHERE !deleted ORDER BY product_type","Getting Product Type Names");
				$ids = "";
				while($row = mysqli_fetch_assoc($producttypes)) {
					$ids .= $row['id'].',';
?>
					<tr>
						<td style="width:10px;"><input type="checkbox" name="allow<?=$row['id']?>" id="allow<?=$row['id']?>" onchange="check(<?=$row['id']?>);" <?=(isset($enabled[$row['id']]) ? ' checked="checked"':'')?> /></td>
						<td><label for='allow<?=$row['id']?>'><?=$row['product_type']?></label></td>
						<td style="width:10px;"><input type="checkbox" name="custom<?=$row['id']?>" id="custom<?=$row['id']?>" <?=(isset($enabled[$row['id']]) ? '':' disabled="disabled"')?><?=(isset($enabled[$row['id']]) && $enabled[$row['id']] == 1 ? ' checked="checked"':'')?> onchange="markchange();" /></td>
						<td><label for='custom<?=$row['id']?>'>Allow Custom Orders</label></td>
					</tr>
<?
				}
?>
				</table>
					<input type="hidden" name="typechanged" id="typechanged" value="0" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="ids" id="ids" value="<?=substr($ids,0,-1)?>" />

			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Update Information'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
			</div>
		</form>
	</div>

<?
	}
?>