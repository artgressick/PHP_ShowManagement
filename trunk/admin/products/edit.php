<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
					
						<div class="colHeader">Product Information</div>

						<?=form_text(array('caption'=>'Real Name','required'=>'true','name'=>'product_name','size'=>'30','maxlength'=>'255','value'=>$info['product_name']))?>
						
						<?=form_text(array('caption'=>'Common Name','name'=>'common_name','size'=>'30','maxlength'=>'255','value'=>$info['common_name']))?>

<?					
						$product_types = db_query("SELECT id, product_type as name FROM product_types WHERE !deleted ORDER BY name","Getting Product Types");
?>
						<?=form_select($product_types,array('caption'=>'Product Type','required'=>'true','name'=>'producttype_id','value'=>$info['producttype_id']))?>

						<div class="colHeader">Platforms</div>
						<div class="required">(Select all that apply)</div>
<?					
						$temp = db_query("SELECT platform_id FROM product_platforms WHERE product_id=".$info['id'],"Get Product Platforms");
						while($row = mysqli_fetch_assoc($temp)) {
							$product_platforms[$row['platform_id']] = true;
						}
						$platforms = db_query("SELECT id, platform_name FROM platforms ORDER BY id","Getting Platforms");
						
						while($row = mysqli_fetch_assoc($platforms)) {
?>
						<div><?=form_checkbox(array('title'=>$row['platform_name'],'array'=>'true','name'=>'platforms','value'=>$row['id'],'checked'=>(isset($product_platforms[$row['id']])?'true':'false'),'extra'=>'onchange="document.getElementById(\'updated_platforms\').value=1;"'))?></div>
<?						
						}
?>
						<input type="hidden" id="updated_platforms" name="updated_platforms" value="0" />

						<div class="colHeader">Session Types</div>
						<div class="required">(Select all that apply)</div>
<?					
						$temp = db_query("SELECT sessiontype_id FROM product_sessiontypes WHERE product_id=".$info['id'],"Get Product Session Types");
						while($row = mysqli_fetch_assoc($temp)) {
							$product_sessiontypes[$row['sessiontype_id']] = true;
						}

						$sessiontypes = db_query("SELECT id, sessiontype_name FROM sessiontypes WHERE !deleted ORDER BY sessiontype_name","Getting Session Types");
						$ids = "";
?>
						<div><input type="button" value="Check All" onclick="checkall('sessiontypes');" /> <input type="button" value="UnCheck All" onclick="uncheckall('sessiontypes');" /></div>
<?						
						while($row = mysqli_fetch_assoc($sessiontypes)) {
							$ids .= $row['id'].',';
?>
						<div><?=form_checkbox(array('title'=>$row['sessiontype_name'],'array'=>'true','name'=>'sessiontypes','value'=>$row['id'],'checked'=>(isset($product_sessiontypes[$row['id']])?'true':'false'),'extra'=>'onchange="document.getElementById(\'updated_sessiontypes\').value=1;"'))?></div>
<?						
						}
?>
						<input type="hidden" id="updated_sessiontypes" name="updated_sessiontypes" value="0" />
						<input type="hidden" name="ids" id="ids" value="<?=substr($ids,0,-1)?>" />
					</td>
					<td class="tcgutter"></td>
					<td class="tcright">
	
						<div class="colHeader">Product Options</div>
						<?=form_checkbox(array('type'=>'radio','caption'=>'Show','title'=>'No','name'=>'enabled','id'=>'enabled0','value'=>'0','required'=>'true','checked'=>(!$info['enabled']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'enabled','id'=>'enabled1','value'=>'1','checked'=>($info['enabled']?'true':'false')))?>
						
						<?=form_checkbox(array('type'=>'radio','caption'=>'Track this Product','title'=>'No','name'=>'track_product','id'=>'track_product0','value'=>'0','required'=>'true','checked'=>(!$info['track_product']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'track_product','id'=>'track_product1','value'=>'1','checked'=>($info['track_product']?'true':'false')))?>
						
						
						
						<?=form_checkbox(array('type'=>'radio','caption'=>'Exclude from Reports','title'=>'No','name'=>'exclude','id'=>'exclude0','value'=>'0','required'=>'true','checked'=>(!$info['exclude']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'exclude','id'=>'exclude1','value'=>'1','checked'=>($info['exclude']?'true':'false')))?>
	
						<?=form_checkbox(array('type'=>'radio','caption'=>'Needs Quantity','title'=>'No','name'=>'needs_quantity','id'=>'needs_quantity0','value'=>'0','required'=>'true','checked'=>(!$info['needs_quantity']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'needs_quantity','id'=>'needs_quantity1','value'=>'1','checked'=>($info['needs_quantity']?'true':'false')))?>
<?					
						$vendors = db_query("SELECT id, vendor_name as name FROM vendors WHERE !deleted ORDER BY name","Getting Vendors");
?>
						<?=form_select($vendors,array('caption'=>'Vendor','required'=>'true','name'=>'vendor_id','value'=>$info['vendor_id']))?>

						<div class="colHeader">Pricing</div>
						
						<?=form_text(array('caption'=>'Charge Price','name'=>'price','size'=>'10','maxlength'=>'20','value'=>$info['price']))?>
						
						<?=form_text(array('caption'=>'Setup Fee','name'=>'setup_fee','size'=>'10','maxlength'=>'20','value'=>$info['setup_fee']))?>
						
						<?=form_text(array('caption'=>'Cost','name'=>'cost','size'=>'10','maxlength'=>'20','value'=>$info['cost']))?>
						
						<div class="colHeader">Shows</div>
						<div class="required">(Select all that apply)</div>
<?					
						$temp = db_query("SELECT show_id FROM product_show WHERE product_id=".$info['id'],"Get Product Shows");
						while($row = mysqli_fetch_assoc($temp)) {
							$product_shows[$row['show_id']] = true;
						}

						$shows = db_query("SELECT id, show_name FROM shows WHERE !deleted ORDER BY show_name","Getting Shows");
						
						while($row = mysqli_fetch_assoc($shows)) {
?>
						<div><?=form_checkbox(array('title'=>$row['show_name'],'array'=>'true','name'=>'shows','value'=>$row['id'],'checked'=>(isset($product_shows[$row['id']])?'true':'false'),'extra'=>'onchange="document.getElementById(\'updated_shows\').value=1;"'))?></div>
<?						
						}
?>
						<input type="hidden" id="updated_shows" name="updated_shows" value="0" />

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