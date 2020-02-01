<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$showinfo;
		
		$cdate = strtotime($showinfo['start_date'].' 00:00:00.0');
		if(!isset($_REQUEST['d']) || !is_numeric($_REQUEST['d'])) { $_REQUEST['d'] = $cdate; }
		$edate = strtotime($showinfo['end_date'].' 00:00:00.0');
		
		$query_times = db_query("SELECT classes.id, time_slots.start_date, time_slots.prep_time, time_slots.start_time, time_slots.end_time, time_slots.strike_time, classes.class_name, classes.lkey
							FROM time_slots 
							JOIN classes ON time_slots.class_id=classes.id 
							WHERE !time_slots.deleted AND time_slots.start_date='".date("Y-m-d",$_REQUEST['d'])."' 
							AND !classes.deleted AND classes.show_id=".$_SESSION['show_id'],"Getting Times");



		$classes = array();					
		while($row = mysqli_fetch_assoc($query_times)) { 
			$cprep = strtotime($row['start_date']." ".$row['prep_time']);
			$cstrike = strtotime($row['start_date']." ".$row['strike_time']);
			$cstart = strtotime($row['start_date']." ".$row['start_time']);
			$cend = strtotime($row['start_date']." ".$row['end_time']);
			$spancnt = round((strtotime($row['start_date']." ".$row['end_time'])-strtotime($row['start_date']." ".$row['start_time']))/900);
			$prepspan = round((strtotime($row['start_date']." ".$row['start_time'])-strtotime($row['start_date']." ".$row['prep_time']))/900);
			$strikespan = round((strtotime($row['start_date']." ".$row['strike_time'])-strtotime($row['start_date']." ".$row['end_time']))/900);
		
			$classes[$row['class_name']] = array(
			"id" => $row['id'],
			"key" => $row['lkey'],
			"prep" => $cprep,
			"strike" => $cstrike,
			"start" => $cstart,
			"end" => $cend,
			"span" => $spancnt,
			"pspan" => $prepspan,
			"sspan" => $strikespan
			);
		}					


			
		$timelimits = db_query("SELECT 
								(SELECT time_slots.prep_time 
								FROM time_slots 
								JOIN classes ON time_slots.class_id=classes.id 
								WHERE !time_slots.deleted AND time_slots.start_date='".date("Y-m-d",$_REQUEST['d'])."' 
								AND !classes.deleted AND classes.show_id=".$_SESSION['show_id']." 
								ORDER BY time_slots.prep_time ASC 
								LIMIT 1) as time_begin, 
								(SELECT time_slots.strike_time 
								FROM time_slots 
								JOIN classes ON time_slots.class_id=classes.id 
								WHERE time_slots.start_date='".date("Y-m-d",$_REQUEST['d'])."' 
								AND !classes.deleted AND classes.show_id=".$_SESSION['show_id']." 
								ORDER BY time_slots.strike_time DESC 
								LIMIT 1) AS time_end","Getting time extremes",1);
									
		$timelimits['time_begin'] = date("Y-m-d",$_REQUEST['d'])." ".$timelimits['time_begin']; 
		
		$timelimits['time_end'] = date("Y-m-d",$_REQUEST['d'])." ".$timelimits['time_end'];
		
		$start_time = strtotime(date("Y-m-d H:00:00.0",strtotime($timelimits['time_begin'])));
		
		$end_time = strtotime(date("Y-m-d H:00:00.0",strtotime($timelimits['time_end']."+1 hour")));
		
		$ctime = $start_time;
		
		$onehour = 3600;
		$fifteen = 900;
?>
	<table cellpadding="0" cellspacing="0" class="tabs" onmouseout="hideinfoBox();" onmouseover="hideinfoBox();">
		<tr>
<?
		while($cdate <= $edate) {
?>		
			<th class="<?=($_REQUEST['d']==$cdate?"current":"tab")?>" onclick="location.href='?d=<?=$cdate?>';"><?=date('F j',$cdate)?></th><th class="space"><!-- BLANK --></th>
<?
			$cdate=$cdate+86400;
		}	
?>
		</tr>
	</table>

	<div class="innerbody" id="innerbody" onmouseout="hideinfoBox();">
<?
	if(count($classes) > 0) {
//	<table cellspacing="0" cellpadding="0" style='width:100%;table-layout: fixed;' class='longviews'>
//	<col width=6>

	//how many columns do we have?
	$t_cols = ($end_time - $start_time) / $fifteen;
	$fix_width = floor(980 / $t_cols);
	$c_time = $start_time;
?>
		<table class="daycal" cellpadding="0" cellspacing="0" style="width:<?=($fix_width*$t_cols)?>px;">
<?
		while($c_time < $end_time) {
		?><col width=<?=$fix_width?>><?
			$c_time = $c_time + $fifteen;
		}
?>
			<tr>
<?
		while($ctime < $end_time) {
?>
				<td class="hour" colspan="4"><?=date('g:i a',$ctime)?></td>
<?
			$ctime = $ctime + $onehour;
		}
?>
			</tr>
<?
		foreach($classes AS $name => $data) {	
			$ctime = $start_time;
?>
			<tr>
<?	
			while($ctime < $end_time) {
				//Prep
				if($ctime == $data['prep'] && $data['prep'] < $data['start']) {
?>
				<td class="<?=(date("H:i",$ctime)==date("H:00",$ctime)?' hour':'')?>"<?=($data['pspan'] > 1?' colspan="'.$data['pspan'].'"':"")?> style="background:yellow; cursor:pointer;" onmouseover="showinfoBox(<?=$data['id']?>);" onmouseout="hideinfoBox();" onclick="location.href='view.php?key=<?=$data['key']?>'">&nbsp;</td>
<?
					if($data['pspan'] > 1) { $ctime=$ctime+(900*$data['pspan']); } else { $ctime = $ctime + $fifteen; }
				//Start
				} else if ($ctime == $data['start']) {
?>
				<td class="sessionitem<?=(date("H:i",$ctime)==date("H:00",$ctime)?' hour':'')?>"<?=($data['span'] > 1?' colspan="'.$data['span'].'"':"")?> style="background:green; cursor:pointer;" onmouseover="showinfoBox(<?=$data['id']?>);" onmouseout="hideinfoBox();" onclick="location.href='view.php?key=<?=$data['key']?>'"><?=$name?></td>
<?
					if($data['span'] > 1) { $ctime=$ctime+(900*$data['span']); } else { $ctime = $ctime + $fifteen; }
				//Strike
				} else if ($ctime == $data['end'] && $data['strike']> $data['end']) {
?>
				<td class="<?=(date("H:i",$ctime)==date("H:00",$ctime)?' hour':'')?>"<?=($data['sspan'] > 1?' colspan="'.$data['sspan'].'"':"")?> style="background:red; cursor:pointer;" onmouseover="showinfoBox(<?=$data['id']?>);" onmouseout="hideinfoBox();" onclick="location.href='view.php?key=<?=$data['key']?>'">&nbsp;</td>
<?
					if($data['sspan'] > 1) { $ctime=$ctime+(900*$data['sspan']); } else { $ctime = $ctime + $fifteen; }
				} else {
?>
				<td<?=(date("H:i",$ctime)==date("H:00",$ctime)?' class="hour"':'')?>>&nbsp;</td>
<?
			$ctime = $ctime + $fifteen;
				}
			}
?>
			</tr>
<?
		}
?>
			<tr>
<?
		$ctime = $start_time;
		while($ctime < $end_time) {
?>
				<td class="hour" colspan="4"><?=date('g:i a',$ctime)?></td>
<?
			$ctime = $ctime + $onehour;
		}
?>
			</tr>
		</table>
		<div id="infobox" class="popup" style="display: none;">
			<div id="infoloading" style="text-align:center;"><?=img(array('src'=>'spinner-1.gif'))?></div>
			<div id="infodata" style="display:none;">
				<table cellpadding="0" cellspacing="0" style="width:300px;">
					<tr>
						<td class="title"><div>Session Name:</div></td>
						<td class="data" id="session_name">&nbsp;</td>
					</tr>
					<tr>
						<td class="title"><div>Session Number:</div></td>
						<td class="data" id="session_number">&nbsp;</td>
					</tr>
					<tr>
						<td class="title"><div>Session Type:</div></td>
						<td class="data" id="session_type">&nbsp;</td>
					</tr>
					<tr>
						<td class="title"><div>Speaker:</div></td>
						<td class="data" id="speaker">&nbsp;</td>
					</tr>
					<tr>
						<td class="title" colspan="2">Dates/Times:</td>
					</tr>
					<tr>
						<td class="" colspan="2" id="datestimes">&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
<?
	} else {
?>
	<div style="text-align:center;">No Sessions found in database for <?=date('F j',$_REQUEST['d'])?></div>
<?
	}
?>
	</div>

	<table class="legend" align="center">
		<tr>
			<th colspan="3">Legend:</th>
		</tr>
		<tr>
			<th>Prep</th>
			<th>Session</th>
			<th>Strike </th>
		</tr>
		<tr>
			<td style="background:yellow;">&nbsp;</td>
			<td style="background:green;">&nbsp;</td>
			<td style="background:red;">&nbsp;</td>
		</tr>
	</table>
	<input type="hidden" id="posX" value="" />
	<input type="hidden" id="posY" value="" />
<?
	}
?>