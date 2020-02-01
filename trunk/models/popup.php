<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?=(isset($title) && $title != '' ? $title.' - ' : '')?><?=$PROJECT_NAME?></title>
<?	if($br->Name == 'MSIE') { ?>		
		<link href="<?=$BF?>includes/globalie.css" rel="stylesheet" type="text/css" />
		<link href="<?=$BF?>includes/menuie.css" rel="stylesheet" type="text/css" media="all" />
<?	} else {?>
		<link href="<?=$BF?>includes/global.css" rel="stylesheet" type="text/css" />
		<link href="<?=$BF?>includes/menu.css" rel="stylesheet" type="text/css" media="all" />
<?	}?>
		<script type='text/javascript'>
			var BF = '<?=$BF?>';
		</script>

<?		# If the "Stuff in the Header" function exists, then call it
		if(function_exists('sith')) { sith(); } 
?>
	</head>
	<body <?=(isset($bodyParams) ? 'onload="'. $bodyParams .'"' : '')?> class="">
<?// echo "<pre>"; print_r($_SESSION); echo "</pre>"; // This is to display the SESSION variables, unrem to use?>
		<table cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
			<tr>
				<td style="height:100%; vertical-align:top;">
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
<?
	# Any aditional things can go down here including javascript or hidden variables
	# "Stuff on the Bottom"
	if(function_exists('sotb')) { sotb(); } 
?>
	</body>
</html>