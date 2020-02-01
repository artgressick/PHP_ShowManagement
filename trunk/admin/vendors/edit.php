<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">

				<div class="colHeader2">Vendor Information</div>

				<?=form_text(array('caption'=>'Vendor Name','required'=>'true','name'=>'vendor_name','size'=>'30','maxlength'=>'150','value'=>$info['vendor_name']))?>

			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Update Information'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
			</div>
		</form>
	</div>

<?
	}
?>