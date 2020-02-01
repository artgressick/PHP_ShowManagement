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
<? 
					if($_SESSION['admin_access'] == 1) {
						$shows = db_query("SELECT shows.id, show_name AS name, status_name AS optGroup FROM shows JOIN show_status ON shows.status_id=show_status.id WHERE !deleted ORDER BY optGroup DESC, shows.start_date","Getting Shows");
					} else {
						$shows = db_query("SELECT id, show_name AS chrRecord FROM shows WHERE !deleted AND status_id=1","getting shows");
					}			
?>
										<?=form_select($shows,array('nocaption'=>'true','caption'=>'- Select Show -','required'=>'true','name'=>'show_id','style'=>'width:100px;','extra'=>'onchange="form.submit();"'))?>
				
	
					</td>
				</tr>
			</table>
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Continue'))?>
			</div>
		</form>
	</div>

<?
	}
?>