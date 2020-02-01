<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">

				<div class="colHeader">Session Category Information</div>

				<?=form_text(array('caption'=>'Session Category Name','required'=>'true','name'=>'sessioncat_name','size'=>'30','maxlength'=>'150','value'=>$info['sessioncat_name']))?>
				
						<?=form_checkbox(array('type'=>'radio','caption'=>'Ignore Hide Room Date','title'=>'No','name'=>'ignorehiddenroom','id'=>'ignorehiddenroom0','value'=>'0','required'=>'true','checked'=>(!$info['ignorehiddenroom']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'ignorehiddenroom','id'=>'ignorehiddenroom1','value'=>'1','checked'=>($info['ignorehiddenroom']?'true':'false')))?>

				
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Update Information'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
			</div>
		</form>
	</div>

<?
	}
?>