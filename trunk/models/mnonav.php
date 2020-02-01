<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Expires" content="Fri, 26 Mar 1999 23:59:59 GMT">
		<meta name="Author" content="techIT Solutions LLC.">
		<meta name="Keywords" content="techIT Business Show Management Solutions">
		<meta name="Description" content="Show Management.">
		<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
		<title><?=(isset($title) ? $title.' - ' : '')?><?=$PROJECT_NAME?> - Mobile</title>
		
		<link href="<?=$BF?>mobile/includes/global.css" rel="stylesheet" type="text/css">
		<script type='text/javascript'>var BF = '<?=$BF?>';</script>
<?		# If the "Stuff in the Header" function exists, then call it
		if(function_exists('sith')) { sith(); } 
?>
	</head>
	<body <?=(isset($bodyParams) ? 'onload="'. $bodyParams .'"' : '')?> style='padding:0; margin:0;'>
<?// echo "<pre>"; print_r($_SESSION); echo "</pre>"; // This is to display the SESSION variables, unrem to use?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" style='height:100%;'>
			<tr>
				<td valign="top" class='header-left'>
					Show Management
				</td>
			</tr>
			<tr>
				<td valign="middle" style='height:100%;'>
					<table border="0" cellspacing="0" cellpadding="0" style='width:100%;'>
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
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" style='height:10px;font-size:8px;'>Copyright &copy; 2000-<?=date('Y')?> techIT Solutions, Inc. All rights reserved.</td>
			</tr>
		</table>
<?
	# Any aditional things can go down here including javascript or hidden variables
	# "Stuff on the Bottom"
	if(function_exists('sotb')) { sotb(); } 
?>
	</body>
</html>