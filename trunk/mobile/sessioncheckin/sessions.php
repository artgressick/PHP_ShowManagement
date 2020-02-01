<?php
	include('_controller.php');

	function sitm() {
		global $BF,$info,$results;
			if(!isset($_REQUEST['building_id'])) { $_REQUEST['building_id'] = ""; }
			if(!isset($_REQUEST['room_id'])) { $_REQUEST['room_id'] = ""; }
?>
		<div style="border:1px solid #999; background:yellow; padding:2px; margin-bottom:10px;">Date: <?=pretty_date($_REQUEST['date'])?> -- Select Session -- <input type="text" id="search_text" value="" onkeyup="look_for(this.value);" size='13' title="Search" /></div>
<?
			$buildings = db_query("SELECT id, building_name AS name FROM buildings WHERE !deleted AND show_id=".$_SESSION['show_id']." ORDER BY name","Get Buildings");
			
			$rooms = db_query("SELECT id, room_name AS name FROM rooms WHERE !deleted AND show_id='".$_SESSION['show_id']."'".(isset($_REQUEST['building_id']) && is_numeric($_REQUEST['building_id']) ? " AND rooms.building_id='".$_REQUEST['building_id']."'": "" )." ORDER BY name","Get Rooms");

?>
		<div>Filter By: <?=form_select($buildings,array('caption'=>'All Buildings','nocaption'=>'true','name'=>'building_id','value'=>$_REQUEST['building_id'],'extra'=>'onchange="location.href=\'?date='.$_REQUEST['date'].'&room_id=&building_id=\'+this.value"','style'=>'width:100px;'))."&nbsp;&nbsp;".form_select($rooms,array('caption'=>'All Rooms','nocaption'=>'true','name'=>'room_id','value'=>$_REQUEST['room_id'],'extra'=>'onchange="location.href=\'?date='.$_REQUEST['date'].'&building_id='.$_REQUEST['building_id'].'&room_id=\'+this.value"','style'=>'width:100px;'))?></div>

	 	<ul class="mobile_list">
<?
		while($row = mysqli_fetch_assoc($results)) {
			$li_bg = '';
			$start_dt = strtotime($row['start_date'].' '.$row['start_time']);
			$now = strtotime('Today');
			if($row['checked_datetime'] != '') {
				$li_bg = " style='background:#92ff92;'";
			} else if($now >= ($start_dt - 900) && $now < $start_dt) {
				$li_bg = " style='background:#f9ff5f;'";
			} else if($now >= $start_dt) {
				$li_bg = " style='background:#ff9696;'";
			}
?>
			<li id="session_<?=$row['id']?>"<?=$li_bg?>><a href="session.php?id=<?=$row['id']?>"><?=$row['class_name']?><br />
				<span style="font-size:12px; font-weight:normal;">
					Prep: <?=pretty_time($row['prep_time'])?> - Start: <?=pretty_time($row['start_time'])?><br />
					Room: <?=$row['room_name']?>
				</span>
			</a></li>
<?				
		}
?>
	</ul>
<?
	}
?>