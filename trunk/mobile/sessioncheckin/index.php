<?php
	include('_controller.php');

	function sitm() {
		global $BF;
?>
		<div style="border:1px solid #999; background:yellow; padding:2px; margin-bottom:10px;">Select Date</div>
	    <ul class="mobile_list">
<?
	$tempdates = db_query("SELECT time_slots.start_date
					FROM time_slots
					JOIN classes ON time_slots.class_id=classes.id
					WHERE !classes.deleted AND !time_slots.deleted
					GROUP BY time_slots.start_date
					ORDER BY time_slots.start_date
					","Get dates");

	while($row = mysqli_fetch_assoc($tempdates)) {
?>
			<li<?=(date('Y-m-d')==$row['start_date']?' style="background:lightgreen;"':'')?>><a href="sessions.php?date=<?=$row['start_date']?>"><?=pretty_date($row['start_date'])?></a></li>
<?
	}
?>
	    </ul>
<?
	}
?>