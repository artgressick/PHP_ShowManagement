<?php
	include('_controller.php');

	function sitm() {
		global $BF,$buildings;
?>
		<div style="border:1px solid #999; background:yellow; padding:2px; margin-bottom:10px;">Select Building -- <input type="text" id="search_text" value="" onkeyup="look_for(this.value);" title="Search" /></div>
	    <ul class="mobile_list">
<?
	while($row = mysqli_fetch_assoc($buildings)) {
?>
			<li id='building_<?=$row['id']?>'><a href="rooms.php?key=<?=$row['lkey']?>"><?=$row['building_name']?></a></li>
<?
	}
?>
	    </ul>
<?
	}
?>