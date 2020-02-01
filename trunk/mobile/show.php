<?
	include('_controller.php');
	
	function sitm() { 
		global $BF;
?>
						<form id="idForm" name="form1" method="post" action="">
							<table border="0" cellspacing="0" cellpadding='5' width='' align='center'>							
								<tr>
									<td align='center'><strong>Select Show</strong></td>
								</tr>
								<tr>
									<td>
<? 
					if($_SESSION['admin_access'] == 1) {
						$shows = db_query("SELECT shows.id, show_name AS name, status_name AS optGroup FROM shows JOIN show_status ON shows.status_id=show_status.id WHERE !deleted ORDER BY optGroup DESC, shows.start_date","Getting Shows");
					} else {
						$shows = db_query("SELECT id, show_name AS name FROM shows WHERE !deleted AND status_id=1","getting shows");
					}			
					if(mysqli_num_rows($shows) == 1) {
						$temp = mysqli_fetch_assoc($shows);
						$showselect = $temp['id'];
						mysqli_data_seek($shows, 0);
					} else {
						$showselect = '';
					}
?>
					
					<?=form_select($shows,array('nocaption'=>'true','caption'=>'- Select Show -','required'=>'true','name'=>'show_id','style'=>'width:200px;','extra'=>'onchange="form.submit();"','value'=>$showselect))?>
				
	
								</tr>
								<tr>
									<td align="center" colspan='1'><?=form_button(array('type'=>'submit','value'=>'Continue'))?></td>
								</tr>
							</table>
						</form>
<?
		if($showselect != '' && !isset($_SESSION['show_id'])) {
?>
	<script type='text/javascript'>document.getElementById('idForm').submit();</script>
<?
		}
	} 
?>