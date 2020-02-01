<?
	require_once($BF.'components/add_functions.php');
	
	//dtn:  April 26, 2007.  Curl importer.  This will get the xml document automatically and then do the update all in one script.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,
	'http://center.uoregon.edu/ISTE/techit/xml_export.php?sessionid='.$info['id']);
	$fp = fopen('_tmp_xml_'.$info['id'].'.xml', 'w');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec ($ch);
	curl_close ($ch);
	fclose($fp);

	$t = file_get_contents('_tmp_xml_'.$info['id'].'.xml');
	$s = simplexml_load_string($t);
	global $mysqli_connection;
	//Now lets get all this data for use later
	$sessioncats = array();
	$temp = db_query("SELECT * FROM sessioncats WHERE !deleted","Get Records");
	while($row = mysqli_fetch_assoc($temp)) {
		$sessioncats[$row['sessioncat_name']] = $row['id'];
	}
	$sessiontypes = array();
	$temp = db_query("SELECT * FROM sessiontypes WHERE !deleted","Get Records");
	while($row = mysqli_fetch_assoc($temp)) {
		$sessiontypes[$row['sessiontype_name']] = $row['id'];
	}
	db_query("UPDATE time_slots SET deleted=1 WHERE class_id=".$info['id']." AND imported","Marks times as deleted");	

	foreach($s->table as $table) {
		if($table['tablename'] == 'sessions') {
			$cnt = 0;
			$db_table = "classes";
			foreach($table->data->session as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE id='".$d['sessionid']."'","Getting Data",1);

				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET 
												sessioncat_id='".$sessioncats[encode($d->category)]."',
												sessiontype_id='".$sessiontypes[encode($d->subcategory)]."',
												status_id='".($d->status == "Accepted"?'3':'5')."',
												class_name='".quoteencode($d->title)."', 
												speaker='".encode($d->presenter->firstname.' '.$d->presenter->lastname)."',
												imported=1, 
												deleted='".($d->status == "Accepted"?'0':'1')."', 
												updated_at=NOW(), 
												show_id='".$test['show_id']."',
												bill_other='".($d->billing_enabled == 1?'1':'0')."',
												bill_name='".encode(($d->billing_enabled == 1?$d->financial->firstname.' '.$d->financial->lastname:''))."',
												bill_address1='".encode(($d->billing_enabled == 1?$d->financial->address:''))."',
												bill_local='".encode(($d->billing_enabled == 1?$d->financial->city:''))."',
												bill_state='".encode(($d->billing_enabled == 1?$d->financial->state:''))."',
												bill_postal='".encode(($d->billing_enabled == 1?$d->financial->zipcode:''))."',
												bill_country='".encode(($d->billing_enabled == 1?$d->financial->country:''))."',
												bill_email='".encode(($d->billing_enabled == 1?$d->financial->email:''))."'
												WHERE id=".$test['id'],"Update Record");
				} else { // Insert New Record
					db_query("INSERT INTO ".$db_table." SET 
												id=".$d['sessionid'].", 
												lkey='".makekey()."', 
												sessioncat_id='".$sessioncats[encode($d->category)]."',
												sessiontype_id='".$sessiontypes[encode($d->subcategory)]."',
												status_id='".($d->status == "Accepted"?'3':'5')."',
												class_name='".quoteencode($d->title)."', 
												speaker='".encode($d->presenter->firstname.' '.$d->presenter->lastname)."',
												imported=1, 
												deleted='".($d->status == "Accepted"?'0':'1')."', 
												show_id=".$show_id.", 
												bill_other='".($d->billing_enabled == 1?'1':'0')."',
												bill_name='".encode(($d->billing_enabled == 1?$d->financial->firstname.' '.$d->financial->lastname:''))."',
												bill_address1='".encode(($d->billing_enabled == 1?$d->financial->address:''))."',
												bill_local='".encode(($d->billing_enabled == 1?$d->financial->city:''))."',
												bill_state='".encode(($d->billing_enabled == 1?$d->financial->state:''))."',
												bill_postal='".encode(($d->billing_enabled == 1?$d->financial->zipcode:''))."',
												bill_country='".encode(($d->billing_enabled == 1?$d->financial->country:''))."',
												bill_email='".encode(($d->billing_enabled == 1?$d->financial->email:''))."',
												created_at=NOW(),updated_at=NOW()
												","Insert Record");
				}

				foreach($d->meetings->meeting as $t) {
					$tmp_times = db_query("SELECT id FROM time_slots WHERE id=".$t['meetingid'],"Test for Time",1);
					if(isset($tmp_times['id']) && is_numeric($tmp_times['id'])) {
					
						db_query("UPDATE time_slots SET 
													deleted=0,
													imported=1,
													class_id=".$d['sessionid'].", 
													start_date='".date('Y-m-d',strtotime($t->sessiondate))."', 
													prep_time='".date('H:i:00.0',strtotime($t->setup_start))."',
													start_time='".date('H:i:00.0',strtotime($t->starttime))."',
													end_time='".date('H:i:00.0',strtotime($t->end_time))."',
													strike_time='".date('H:i:00.0',strtotime($t->teardown_end))."', 
													room_id='".$t->roomid."',
													room_area='".encode($t->room_area)."',
													description='".encode($t->description)."'
													WHERE id=".$t['meetingid'],"Insert Time Record");
					
					} else {
						db_query("INSERT INTO time_slots SET 
													id=".$t['meetingid'].",
													imported=1,
													class_id=".$d['sessionid'].", 
													start_date='".date('Y-m-d',strtotime($t->sessiondate))."', 
													prep_time='".date('H:i:00.0',strtotime($t->setup_start))."',
													start_time='".date('H:i:00.0',strtotime($t->starttime))."',
													end_time='".date('H:i:00.0',strtotime($t->end_time))."',
													strike_time='".date('H:i:00.0',strtotime($t->teardown_end))."', 
													room_id='".$t->roomid."',
													room_area='".encode($t->room_area)."',
													description='".encode($t->description)."'
													","Insert Time Record");
					}

				}
				//Remove all unused times
				db_query("DELETE FROM time_slots WHERE imported AND deleted AND class_id=".$d['sessionid'],"Deletes all unused times");
				
				$cnt++;
			}
		}

	}
	unlink('_tmp_xml_'.$info['id'].'.xml');
?>
