<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
		<title><?=(isset($title) && $title != '' ? $title.' - ' : '')?><?=$PROJECT_NAME?> - Mobile</title>
		<link href="<?=$BF?>mobile/includes/global.css" rel="stylesheet" type="text/css" />
		<script type='text/javascript'>
			var BF = '<?=$BF?>';
		</script>

<?		# If the "Stuff in the Header" function exists, then call it
		if(function_exists('sith')) { sith(); } 
?>
	</head>
	<body <?=(isset($bodyParams) ? 'onload="'. $bodyParams .'"' : '')?>>
<?// echo "<pre>"; print_r($_SESSION); echo "</pre>"; // This is to display the SESSION variables, unrem to use?>

		<table width="100%" border="0" cellspacing="0" cellpadding="0" class='frame' style='height:100%;' >
			<tr>
				<td valign="top" class='header-left'>
					<a href="<?=$BF?>mobile/" style="color:black;">Show Management</a>
				</td>
				<td valign="top" class='header-left'>
					<?=date('m/d/Y');?>
				</td>
				<td valign="top" class='header-right'>
					<a href="?logout=1" style="color:black;">Log-out</a>
				</td>
			</tr>
			<tr>
				<td colspan='3' valign="middle" style='height:100%;'>
					<table border="0" cellspacing="0" cellpadding="0" style='width:100%; height:100%;'>
						<tr>
							<td style='padding:10px; height:100%; vertical-align:top;'>
								<?=messages()?>
<!-- Begin code -->
<?
	# This is where we will put in the code for the page.
	(!isset($sitm) || $sitm == '' ? sitm() : $sitm());
?>
<!-- End code -->
							</td>
						</tr>						
					</table>
				</td>
			</tr>
			<tr>
				<td colspan='3' align="center" style='height:10px;font-size:8px;'>Copyright &copy; 2000 - <?=date('Y')?> techIT Solutions, Inc. All rights reserved.</td>
			</tr>
		</table>
<?

	# Any aditional things can go down here including javascript or hidden variables
	# "Stuff on the Bottom"
	if(function_exists('sotb')) { sotb(); } 
?>
	</body>
</html>