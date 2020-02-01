<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">

				<div class="colHeader">Session Category Information</div>

				<?=form_text(array('caption'=>'Session Category Name','required'=>'true','name'=>'sessioncat_name','size'=>'30','maxlength'=>'150'))?>

<?=form_checkbox(array('type'=>'radio','caption'=>'Ignore Hide Room Date','title'=>'No','name'=>'ignorehiddenroom','id'=>'ignorehiddenroom0','value'=>'0','required'=>'true','checked'=>'false'))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'ignorehiddenroom','id'=>'ignorehiddenroom1','value'=>'1','checked'=>'true'))?>


			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Add Another','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'add.php\';"'))?> &nbsp;&nbsp; <?=form_button(array('type'=>'submit','value'=>'Add and Continue','extra'=>'onclick="document.getElementById(\'moveTo\').value=\'index.php\';"'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'moveTo'))?>
			</div>
		</form>
	</div>

<?
	}
?>