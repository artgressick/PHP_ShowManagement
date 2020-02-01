<?
	include('_controller.php');

	function sitm() { 
		global $BF;
		if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) { $_REQUEST['id'] = 4; } 
?>
	<div class='header2'>Logged Off</div>
	<?=messages()?>
	<p>You are now logged off. &nbsp;Possible reasons this has occurred may be:</p>
	<p>
		<ul style="line-height:18px;">
			<li<?=($_REQUEST['id']==1?' style="font-weight:bold;"':'')?>>You clicked on the logoff link.</li>
			<li<?=($_REQUEST['id']==0?' style="font-weight:bold;"':'')?>>The link between our webserver and your browser has been broken.</li>
			<li<?=($_REQUEST['id']==2?' style="font-weight:bold;"':'')?>>You have stayed on the same page for longer than 20 minutes.</li>
			<li<?=($_REQUEST['id']==3?' style="font-weight:bold;"':'')?>>You have been logged in for more then 12 Hours, and should take a break.</li>
		</ul>
	</p>
	<p style='text-align:center;'><?=linkto(array('address'=>'index.php','display'=>'Please click here to log in again.'))?></p>

<?
//	unset($_SESSION['logoutReason']);
	} ?>