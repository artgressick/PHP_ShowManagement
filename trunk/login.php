<?
	function litm() { 
		global $BF;
		if(isset($_COOKIE['email']) && $_COOKIE['email'] != '') {
			$_REQUEST['email'] = $_COOKIE['email'];
		}
?>
						<form id="idForm" name="form1" method="post" action="">
							<table border="0" cellspacing="0" cellpadding='5' width='350' align='center'>							
								<tr>
									<td align='right'><strong>Email Address</strong></td>
									<td>
										<?=form_text(array('nocaption'=>'true','size'=>'30','caption'=>'Username','name'=>'auth_form_name','value'=>(isset($_REQUEST['email']) && $_REQUEST['email'] != "" ? $_REQUEST['email'] : (isset($_REQUEST['auth_form_name']) ? $_REQUEST['auth_form_name'] : ''))))?>
									</td>
								</tr>
								<tr>
									<td align="right"><strong>Password</strong></td>
									<td>
										<?=form_text(array('type'=>'password','nocaption'=>'true','caption'=>'Password','name'=>'auth_form_password','size'=>'30'))?>
									</td>
								</tr>
								<tr>
									<td align="center" colspan='2'><input type="submit" name="button" id="button" value="Log In Now"></td>
								</tr>
								<tr>
									<td colspan='2' style='text-align:center;'>
										<a href="<?=$BF?>lostpassword/">Did you forget your password? Send me a new password</a>		
									</td>
								</tr>
									
							</table>
						</form>
<?
	if($_REQUEST['email'] != '') {
?>
	<script type='text/javascript'>
		document.getElementById('auth_form_password').focus()
	</script>
<?		
	}
	} 
?>