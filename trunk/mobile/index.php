<?php
	include('_controller.php');

	function sitm() {
		global $BF;
?>
		<div>Welcome to the iPhone version of Show Management, This is a scaled down version of the site to help control certain aspects of the event. For full information please goto the Show Management web-site using a Computer</div>
		<div style="margin-top:20px;">
			<div><a class="blue button" href="<?=$BF?>mobile/roomcheckin">Room Checkin</a></div>
			<div><a class="blue button" href="<?=$BF?>mobile/sessioncheckin">Session Checkin</a></div>
<?
		if($_SESSION['admin_access']) {
?>
			<div><a class="blue button" href="<?=$BF?>mobile/checkout.php">Product Check-Out</a></div>
			<div><a class="blue button" href="<?=$BF?>mobile/checkin.php">Product Check-In</a></div>
<?
		}
?>
		</div>			
<?
	}
?>