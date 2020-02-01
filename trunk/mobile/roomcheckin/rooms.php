<?php
	include('_controller.php');

	function sitm() {
		global $BF,$info,$rooms;
?>
		<div style="border:1px solid #999; background:yellow; padding:2px; margin-bottom:10px;">Building: <?=$info['building_name']?> -- Select Room -- <input type="text" id="search_text" value="" onkeyup="look_for(this.value);" size='14' title="Search" /></div>
	 	<ul class="mobile_list">
<?
		while($row = mysqli_fetch_assoc($rooms)) {
?>
			<li id='room_<?=$row['id']?>'><a href="room.php?key=<?=$row['lkey']?>"><?=$row['room_name'].($row['description'] != ''?'<br><span style="font-size:14px;">'.$row['description'].'</span>':'')?></a></li>
<?				
		}
?>
	</ul>
<?
	}
?>