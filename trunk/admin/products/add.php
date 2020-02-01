<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:100%;'>
				<tr>
					<td class="tcleft">
					
						<div class="colHeader">Product Information</div>

						<?=form_text(array('caption'=>'Real Name','required'=>'true','name'=>'product_name','size'=>'30','maxlength'=>'255'))?>
						
						<?=form_text(array('caption'=>'Common Name','name'=>'common_name','size'=>'30','maxlength'=>'255'))?>

<?					
						$product_types = db_query("SELECT id, product_type as name FROM product_types WHERE !deleted ORDER BY name","Getting Product Types");
?>
						<?=form_select($product_types,array('caption'=>'Product Type','required'=>'true','name'=>'producttype_id'))?>

						<div class="colHeader">Platforms</div>
						<div class="required">(Select all that apply)</div>
<?					
						$platforms = db_query("SELECT id, platform_name FROM platforms ORDER BY id","Getting Platforms");
						
						while($row = mysqli_fetch_assoc($platforms)) {
?>
						<div><?=form_checkbox(array('title'=>$row['platform_name'],'array'=>'true','name'=>'platforms','value'=>$row['id']))?></div>
<?						
						}
?>

						<div class="colHeader">Session Types</div>
						<div class="required">(Select all that apply)</div>
<?					
						$sessiontypes = db_query("SELECT id, sessiontype_name FROM sessiontypes WHERE !deleted ORDER BY sessiontype_name","Getting Session Types");
						$ids = "";
?>
						<div><input type="button" value="Check All" onclick="checkall('sessiontypes');" /> <input type="button" value="UnCheck All" onclick="uncheckall('sessiontypes');" /></div>
<?						
						while($row = mysqli_fetch_assoc($sessiontypes)) {
							$ids .= $row['id'].',';
?>
						<div><?=form_checkbox(array('title'=>$row['sessiontype_name'],'array'=>'true','name'=>'sessiontypes','value'=>$row['id']))?></div>
<?						
						}
?>
						<input type="hidden" name="ids" id="ids" value="<?=substr($ids,0,-1)?>" />

					</td>
					<td class="tcgutter"></td>
					<td class="tcright">
	
						<div class="colHeader">Product Options</div>
						<?=form_checkbox(array('type'=>'radio','caption'=>'Show','title'=>'No','name'=>'enabled','id'=>'enabled0','value'=>'0','required'=>'true','checked'=>'false'))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'enabled','id'=>'enabled1','value'=>'1','checked'=>'true'))?>

						<?=form_checkbox(array('type'=>'radio','caption'=>'Track this Product','title'=>'No','name'=>'track_product','id'=>'track_product0','value'=>'0','required'=>'true','checked'=>'true'))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'track_product','id'=>'track_product1','value'=>'1','checked'=>'false'))?>


<?=form_checkbox(array('type'=>'radio','caption'=>'Exclude from Reports','title'=>'No','name'=>'exclude','id'=>'exclude0','value'=>'0','required'=>'true','checked'=>'true'))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'exclude','id'=>'exclude1','value'=>'1','checked'=>'false'))?>

						<?=form_checkbox(array('type'=>'radio','caption'=>'Needs Quantity','title'=>'No','name'=>'needs_quantity','id'=>'needs_quantity0','value'=>'0','required'=>'true','checked'=>'false'))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'needs_quantity','id'=>'needs_quantity1','value'=>'1','checked'=>'true'))?>

	
<?					
						$vendors = db_query("SELECT id, vendor_name as name FROM vendors WHERE !deleted ORDER BY name","Getting Vendors");
?>
						<?=form_select($vendors,array('caption'=>'Vendor','required'=>'true','name'=>'vendor_id'))?>

						<div class="colHeader">Pricing</div>
						
						<?=form_text(array('caption'=>'Charge Price','name'=>'price','size'=>'10','maxlength'=>'20'))?>
						
						<?=form_text(array('caption'=>'Setup Fee','name'=>'setup_fee','size'=>'10','maxlength'=>'20'))?>
						
						<?=form_text(array('caption'=>'Cost','name'=>'cost','size'=>'10','maxlength'=>'20'))?>
						
						<div class="colHeader">Shows</div>
						<div class="required">(Select all that apply)</div>
<?					
						$shows = db_query("SELECT id, show_name FROM shows WHERE !deleted ORDER BY show_name","Getting Shows");
						
						while($row = mysqli_fetch_assoc($shows)) {
?>
						<div><?=form_checkbox(array('title'=>$row['show_name'],'array'=>'true','name'=>'shows','value'=>$row['id'],'checked'=>($_SESSION['show_id']==$row['id']?'true':'false')))?></div>
<?						
						}
?>

					</td>
				</tr>
			</table>

			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Add Another','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'add.php\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'submit','value'=>'Add and Continue','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'index.php\';"'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'moveTo'))?>
			</div>
		</form>
	</div>

<?
	}
?>