<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Expires" content="Fri, 26 Mar 1999 23:59:59 GMT">
		<meta name="Author" content="techIT Solutions LLC.">
		<meta name="Keywords" content="techIT Business Show Management Solutions">
		<meta name="Description" content="Show Management.">

		<title><?=(isset($title) ? $title.' - ' : '')?><?=$PROJECT_NAME?></title>
		
<?	require_once($BF.'components/browser.php');
	$br = new Browser;
	if($br->Name == 'MSIE') { ?>		
		<link href="<?=$BF?>includes/globalie.css" rel="stylesheet" type="text/css" />
<?	} else {?>
		<link href="<?=$BF?>includes/global.css" rel="stylesheet" type="text/css" />
<?	}?>		<script type='text/javascript'>var BF = '<?=$BF?>';</script>
<?		# If the "Stuff in the Header" function exists, then call it
		if(function_exists('sith')) { sith(); } 
?>
	</head>
	<body <?=(isset($bodyParams) ? 'onload="'. $bodyParams .'"' : '')?>>
<?// echo "<pre>"; print_r($_SESSION); echo "</pre>"; // This is to display the SESSION variables, unrem to use?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" style='height:100%;'>
			<tr>
				<td valign="middle">
					<table width="510" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="<?=$BF?>images/login_top.gif" width="510" height="167" alt='Top Logo'></td>
						</tr>
						<tr>
							<td style='background: url(<?=$BF?>images/login_bg.gif);'>
								<table border="0" cellspacing="0" cellpadding="5" style='width:100%;'>
									<tr>
										<td style='padding:10px;'>
											<?=messages()?>
<!-- Begin code -->
<?
	# This is where we will put in the code for the page.
	(!isset($sitm) || $sitm == '' ? sitm() : $sitm());
?>
<!-- End code -->
										</td>
									</tr>						
									<tr>
										<td align="center"><span class="style2">Copyright &copy; 2000-<?=date('Y')?> techIT Solutions, Inc. All rights reserved.</span></td>
									</tr>
		
								</table>
							</td>
						</tr>
						<tr>
							<td><img src="<?=$BF?>images/login_bottom.gif" width="510" height="8" alt='Bottom Logo'></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?
	# Any aditional things can go down here including javascript or hidden variables
	# "Stuff on the Bottom"
	if(function_exists('sotb')) { sotb(); } 
?>
	</body>
</html>