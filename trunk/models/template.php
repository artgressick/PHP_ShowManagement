<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?=(isset($title) && $title != '' ? $title.' - ' : '')?><?=$PROJECT_NAME?></title>
<? if($br->Name == 'MSIE') { ?>		
<? if($br->Version >= 8) { ?>	
		<link href="<?=$BF?>includes/globalie8.css" rel="stylesheet" type="text/css" />
		<link href="<?=$BF?>includes/menuie8.css" rel="stylesheet" type="text/css" media="all" />
<? } else { ?>
		<link href="<?=$BF?>includes/globalie.css" rel="stylesheet" type="text/css" />
		<link href="<?=$BF?>includes/menuie.css" rel="stylesheet" type="text/css" media="all" />
<? } ?>
<? } else {?>
		<link href="<?=$BF?>includes/global.css" rel="stylesheet" type="text/css" />
		<link href="<?=$BF?>includes/menu.css" rel="stylesheet" type="text/css" media="all" />
<? }?>
		<link href="<?=$BF?>components/cool_calendar/cool_calendar.css" rel="stylesheet" type="text/css" media="all" />
		<script type='text/javascript'>
			var BF = '<?=$BF?>';
		</script>
<?		# If the "Stuff in the Header" function exists, then call it
		if(function_exists('sith')) { sith(); } 
?>
	</head>
	<noscript>You must install Javascript to use this site. Get it <a href="http://www.java.com/getjava/">here</a>!</noscript> 
	<body <?=(isset($bodyParams) ? 'onload="'. $bodyParams .'"' : '')?> class="mainbody">
<?// echo "<pre>"; print_r($_SESSION); echo "</pre>"; // This is to display the SESSION variables, unrem to use?>
		<table align="center" class="body_frame" cellpadding="0" cellspacing="0">
			<tr>
				<td class="body_logo"><?=img(array('src'=>'showman-logo.gif','alt'=>$PROJECT_NAME))?></td>
				<td class="body_logo" style='text-align:right;'><?=linkto(array('display'=>'Logout','address'=>'?logout=1','img'=>'logoff.gif'))?></td>
			</tr>
			<tr>
				<td colspan="2" class="body_info">
					<table cellpadding="0" cellspacing="0" style="width:100%;">
						<tr>
							<td style="width:50%;" class="information_section">
								<div style="font-weight:bold; font-size:16px;"><?=(!$_SESSION['auto_logged']?linkto(array('address'=>'/show.php?s=1','display'=>(isset($_SESSION['show_name'])?$_SESSION['show_name']:'Select a Show for more options'))):$_SESSION['show_name'])?></div>
								
							</td>
							<td style="width:50%;">
								<table cellpadding="0" cellspacing="0" style="width:100%;">
									<tr>
										<td style="height:64px;" class="user_information">
											<div>Welcome, <?=$_SESSION['first_name'].' '.$_SESSION['last_name']?></div>
										</td>
									</tr>
									<tr>
										<td style="height:36px;vertical-align:top;">
											<div>
												<div id="headernav1" class="menu">
													<ul id="primary_nav">
<?
														if(isset($_SESSION['show_id']) && is_numeric($_SESSION['show_id']) && !$_SESSION['auto_logged']) {
?>														
													
														<li class="levelone">
															<a href="">Sessions</a>
															<ul<?=($br->Name == 'MSIE'?' style="margin-left: -84px;"':"")?>>
																<li>
																	<a href="<?=$BF?>sessions/index.php">Session List</a>
																</li>
																<li>
																	<a href="<?=$BF?>sessions/rooms.php">Room List</a>
																</li>
																<li>
																	<a href="<?=$BF?>sessions/dayview.php">Day Calendar View</a>
																</li>
															</ul>
														</li>
														<li class="levelone">
															<a href="#">Reports</a>
															<ul<?=($br->Name == 'MSIE'?' style="margin-left: -76px;"':"")?>>
																<li>
																	<a href="<?=$BF?>reports/sessions.php">Sessions Report</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/session_products.php">Session Products</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/session_requests.php">Session Requests</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/session_requests2.php">Session Requests Single Lines</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/no_orders.php">Sessions With No Orders</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/room_products.php">Room Products</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/rooms_internet.php">Rooms with Internet</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/rooms_access.php">Room Access Times</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/sessions_checked.php">Sessions Checked In</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/rooms_checked.php">Rooms Checked In</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/tracking.php">Product Tracking</a>
																</li>
																<li>
																	<a href="<?=$BF?>reports/room_session_products.php">Room/Session Products</a>
																</li>
															</ul>
														</li>
<?
														}
													if($_SESSION['admin_access'] || ($_SESSION['group_id'] == 4 && isset($_SESSION['show_id']))) {
?>
														<li class="levelone">
															<a href="<?=$BF?>admin/">Administration</a>
															<ul<?=($br->Name == 'MSIE'?' style="margin-left: -114px;"':"")?>>
<?
														if(isset($_SESSION['show_id']) && is_numeric($_SESSION['show_id'])) {
?>														
																<li>
																	<a href="<?=$BF?>admin/orderfulfill/index.php">Order Fulfillment</a>
																</li>
<?
															if($_SESSION['admin_access']) {
?>															
																<li>
																	<a href="<?=$BF?>admin/sessions/">Session Management</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/rooms/">Room Management</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/buildings/">Building Management</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/product_tracking/">Product Tracking</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/product_tracking/resettracking.php">Correct Product Tracking</a>
																</li>

<?
															}
														}
														if($_SESSION['admin_access']) {
?>
																<li>
																	<a href="<?=$BF?>admin/products/">Products</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/producttypes/">Product Types</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/pt_questions/">Product Type Questions</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/vendors/">Vendors</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/sessiontypes/">Session Types</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/sessioncats/">Session Categories</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/shows/">Show Management</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/users/">User Management</a>
																</li>
																<li>
																	<a href="<?=$BF?>admin/master_import.php">Import from ISTE</a>
																</li>
<?
														}
?>
															</ul>
														</li>
<?
													}
?>
													</ul>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="body_spacer"><?=img(array('src'=>'_blank.gif','alt'=>"blank"))?></td>
			</tr>
<?
		if(!$_SESSION['auto_logged']) {
?>
			<tr>
				<td colspan="2" class="body_nav">
					<table class="nav_bar" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="nbleft" onclick="location.href='<?=$BF?>index.php'" title="Home"><?=img(array('src'=>'_blank.gif','alt'=>"blank"))?></td>
<?
						$nbc = 0;
						foreach($breadcrumbs AS $k => $d) {
							if($nbc++ > 0) { 
?>
							<td class="nbdiv"><?=img(array('src'=>'_blank.gif','alt'=>"blank"))?></td>
<?
							}
?>							
							<td class="nbmiddle" style=""<?=(isset($d['URL']) && $d['URL'] != ''?' onclick="location.href=\''.$d['URL'].'\'" title="'.$d['TEXT'].'" style="cursor:pointer;"':'')?>><div style="white-space: nowrap;"><?=$d['TEXT']?></div></td>
<?							
						}
?>							
							<td class="nbfill">&nbsp;</td>
							<td class="nbright"><?=img(array('src'=>'_blank.gif','alt'=>"blank"))?></td>
						</tr>
					</table>
				</td>
			</tr>
<?
		}
?>
			<tr>
				<td colspan="2" class="body_inner">
<?
			if(isset($page_info) && is_array($page_info)) {
?>
					<table cellpadding="0" cellspacing="0" class="page_info" border="0">
						<tr>
							<td class="headleft" width="4">&nbsp;</td>
<?
						if(isset($page_info['add_link'])) {
?>
							<td class="headmiddle" style="width:10px;"><?=linkto(array('address'=>$page_info['add_link'],'img'=>'add.png','display'=>"Add"))?></td>
<?						
						}
						if(isset($page_info['add_popup'])) {
?>
							<td class="headmiddle" style="width:10px;"><?=img(array('src'=>'add.png','alt'=>"Add",'style'=>'cursor:pointer;','extra'=>'onclick="location.href=\''.$page_info['add_popup'].'\'"'))?></td>
<?						
						}

?>							
							<td class="headmiddle" style="padding-left:5px;"><?=$page_info['title']?></td>
<?
						if(isset($page_info['title_right'])) {
?>
							<td class="headmiddle" style="padding-right:5px; text-align:right;"><?=$page_info['title_right']?></td>
<?
						}
?>							
							<td class="headright">&nbsp;</td>
						</tr>
						<tr>
							<td class="body" colspan="5"><?=$page_info['instructions']?></td>
						</tr>
					</table>
<?								
			}	
?>					
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
				<td colspan="2" class="body_footer">
					<div>Copyright © 2000-<?=date('Y')?> techIT Solutions Inc.</div>
					<div>Show Management Version 3.1 is a product of techIT Solutions.</div>
					<div>For more information on our products and services please visit our <a href="http://www.techitsolutions.com" target="_blank">website</a>.</div>
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