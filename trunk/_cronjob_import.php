#!/usr/bin/php
<?
	require('/home/techit/php_includes/showmanagement-conf.php');
	if(!isset($host)) {
		$connected = false;
		die();
	} else {
		$connected = true;
		if($mysqli_connection = @mysqli_connect($host, $user, $pass)) {
			if(!@mysqli_select_db($mysqli_connection, $db)) {
				die();
			}
		} else {
			die();
		}
	}
	// clean up so that these variables aren't exposed through the debug console
	unset($host, $user, $pass, $db);

	$BF = ""; //This is the BASE FOLDER.  This should be located at the top of every page with the proper set of '../'s to find the root folder 
	$begin_time = microtime(true);
	$show_id = 1;
	require_once($BF.'components/add_functions.php');
	echo "
	
	Starting XML Master Import.
	";
	ob_flush();
	flush();

	//dtn:  April 26, 2007.  Curl importer.  This will get the xml document automatically and then do the update all in one script.
	if(!isset($_REQUEST['noimport'])) {
		echo "Getting Data From ISTE ... ";
		ob_flush();
		flush();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,
		'http://center.uoregon.edu/ISTE/techit/xml_export.php?password=techitcanseethis');
		$fp = fopen('xml_report.xml', 'w');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_exec ($ch);
		curl_close ($ch);
		fclose($fp);
		echo "Done.
		";
		ob_flush();
		flush();
	}

	// Mark Imported Items as deleted (This is incase they remove something we won't have to worry about it later)
	echo "Mark Old Imported Items as Deleted ... ";	
	ob_flush();
	flush();
	db_query("UPDATE sessioncats SET deleted=1 WHERE imported=1","Mark Session Categories as Deleted");
	db_query("UPDATE sessiontypes SET deleted=1 WHERE imported=1","Mark Session Types as Deleted");
	db_query("UPDATE buildings SET deleted=1 WHERE imported=1","Mark Buildings as Deleted");
	db_query("UPDATE rooms SET deleted=1 WHERE imported=1","Mark Rooms as Deleted");
	db_query("UPDATE classes SET deleted=1 WHERE imported=1","Mark Sessions as Deleted");
	db_query("UPDATE speakers SET deleted=1 WHERE imported=1","Mark Sessions as Deleted");
	db_query("UPDATE time_slots SET deleted=1 WHERE imported","Marks all imports as deleted");	
	echo "Done.
	";
	ob_flush();
	flush();

	$t = file_get_contents('xml_report.xml');
	$s = simplexml_load_string($t);
	global $mysqli_connection;

	foreach($s->table as $table) {

		if($table['tablename'] == 'categories') {
			echo "Importing Session Categories ... ";
			ob_flush();
			flush();
			$cnt = 0;
			$db_table = "sessioncats";
			foreach($table->data->category as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE sessioncat_name='".encode($d->category_literal)."'","Getting Data",1);
				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET imported=1, deleted=0, updated_at=NOW() WHERE id=".$test['id'],"Update Record");
				} else { // Insert New Record
					db_query("INSERT INTO ".$db_table." SET imported=1, 
															lkey='".makekey()."', 
															sessioncat_name = '".encode($d->category_literal)."',
															created_at=NOW(),updated_at=NOW()
															","Insert Record");
				}
				$cnt++;
			}
			//Now lets get all this data for use later
			$sessioncats = array();
			$temp = db_query("SELECT * FROM ".$db_table." WHERE !deleted","Get Records");
			while($row = mysqli_fetch_assoc($temp)) {
				$sessioncats[$row['sessioncat_name']] = $row['id'];
			}
			ob_flush();
			flush();
			echo 'Complete! (Imported '.$cnt.' records)
			';

		} else if($table['tablename'] == 'subcategories') {

			echo "Importing Session Types ... ";
			ob_flush();
			flush();
			$cnt = 0;
			$db_table = "sessiontypes";
			foreach($table->data->subcategory as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE sessiontype_name='".encode($d->subcategory_literal)."'","Getting Data",1);
				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET imported=1, deleted=0, updated_at=NOW() WHERE id=".$test['id'],"Update Record");
				} else { // Insert New Record
					db_query("INSERT INTO ".$db_table." SET imported=1, 
															lkey='".makekey()."', 
															sessiontype_name = '".encode($d->subcategory_literal)."',
															created_at=NOW(),updated_at=NOW()
															","Insert Record");
				}
				$cnt++;
			}
			//Now lets get all this data for use later
			$sessiontypes = array();
			$temp = db_query("SELECT * FROM ".$db_table." WHERE !deleted","Get Records");
			while($row = mysqli_fetch_assoc($temp)) {
				$sessiontypes[$row['sessiontype_name']] = $row['id'];
			}
			echo 'Complete! (Imported '.$cnt.' records)
			';
			ob_flush();
			flush();
			
		} else if($table['tablename'] == 'buildings') {

			echo "Importing Buildings ... ";
			ob_flush();
			flush();
			$cnt = 0;
			$db_table = "buildings";
			foreach($table->data->building as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE id='".$d['buildingid']."'","Getting Data",1);
				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET building_name='".encode($d->buildingname)."', notes='".encode($d->building_descr)."', imported=1, deleted=0, updated_at=NOW(), show_id=".$show_id." WHERE id=".$test['id'],"Update Record");
				} else { // Insert New Record
					db_query("INSERT INTO ".$db_table." SET id=".$d['buildingid'].",
															imported=1,
															lkey='".makekey()."',
															show_id=".$show_id.",
															building_name='".encode($d->buildingname)."',
															notes='".encode($d->building_descr)."',
															created_at=NOW(),updated_at=NOW()
															","Insert Record");
				}
				$cnt++;
			}
			echo 'Complete! (Imported '.$cnt.' records)
			';
			ob_flush();
			flush();

		} else if($table['tablename'] == 'rooms') {

			echo "Importing Rooms ... ";
			ob_flush();
			flush();
			$cnt = 0;
			$db_table = "rooms";
			foreach($table->data->room as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE id='".$d['roomid']."'","Getting Data",1);
				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET 
												building_id='".$d->buildingid."',
												room_number='".encode($d->room_number)."', 
												room_name='".encode($d->room_number)."', 
												description='".encode($d->room_descr)."', 
												capacity='".encode($d->room_capacity)."', 
												square_feet='".encode($d->square_feet)."', 
												dimensions='".encode($d->dimensions)."', 
												public_location='".encode($d->published_location)."', 
												notes='".encode($d->notes)."', 
												category='".encode($d->category)."', 
												default_setup='".encode($d->default_setup)."', 
												move_in='".date('Y-m-d H:i:00.0',strtotime($d->movein_date.' '.$d->movein_time))."', 
												move_out='".date('Y-m-d H:i:00.0',strtotime($d->moveout_date.' '.$d->moveout_time))."', 
												imported=1, 
												deleted=0, 
												updated_at=NOW(), 
												show_id=".$show_id." 
												WHERE id=".$test['id'],"Update Record");
				} else { // Insert New Record
					db_query("INSERT INTO ".$db_table." SET 
												id=".$d['roomid'].", 
												lkey='".makekey()."', 
												building_id='".$d->buildingid."',
												room_number='".encode($d->room_number)."', 
												room_name='".encode($d->room_number)."', 
												description='".encode($d->room_descr)."', 
												capacity='".encode($d->room_capacity)."', 
												square_feet='".encode($d->square_feet)."', 
												dimensions='".encode($d->dimensions)."', 
												public_location='".encode($d->published_location)."', 
												notes='".encode($d->notes)."', 
												category='".encode($d->category)."', 
												default_setup='".encode($d->default_setup)."', 
												move_in='".date('Y-m-d H:i:00.0',strtotime($d->movein_date.' '.$d->movein_time))."', 
												move_out='".date('Y-m-d H:i:00.0',strtotime($d->moveout_date.' '.$d->moveout_time))."', 
												imported=1, 
												deleted=0, 
												show_id=".$show_id.", 
												created_at=NOW(),updated_at=NOW()
												","Insert Record");
				}
				$cnt++;
			}
			echo 'Complete! (Imported '.$cnt.' records)
			';
			ob_flush();
			flush();

		} else 
		
		if($table['tablename'] == 'sessions') {
			echo "Importing Sessions ... ";
			ob_flush();
			flush();
			$cnt = 0;
			$db_table = "classes";
			foreach($table->data->session as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE id='".$d['sessionid']."'","Getting Data",1);

				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET 
												sessioncat_id='".$sessioncats[encode($d->category)]."',
												sessiontype_id='".$sessiontypes[encode($d->subcategory)]."',
												speaker_id='".encode($d->presenter)."',
												financial_id='".encode($d->financial)."',
												billing_enabled='".encode($d->billing_enabled)."',
												status_id='".($d->status == "Accepted"?'3':'5')."',
												class_name='".quoteencode($d->title)."', 
												speaker='".encode($d->presenter->firstname.' '.$d->presenter->lastname)."',
												imported=1, 
												deleted='".($d->status == "Accepted"?'0':'1')."', 
												updated_at=NOW(), 
												show_id=".$show_id.",
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
												speaker_id='".encode($d->presenter)."',
												financial_id='".encode($d->financial)."',
												billing_enabled='".encode($d->billing_enabled)."',
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
			
			echo 'Complete! (Imported '.$cnt.' records)
			';
			ob_flush();
			flush();
		//new information for showman 2010
		} else if($table['tablename'] == 'speakers') {
		
			echo "Importing Speakers ... ";
			ob_flush();
			flush();
			$cnt = 0;
			$db_table = "speakers";
			foreach($table->data->speaker as $d) {
				//Lets see if we have a value before we insert it
				$test = db_query("SELECT * FROM ".$db_table." WHERE id='".$d['speakerid']."'","Getting Data",1);
				if(isset($test['id']) && is_numeric($test['id'])) { // Update
					db_query("UPDATE ".$db_table." SET 
							first_name='".encode($d->firstname)."', 
							last_name='".encode($d->lastname)."', 
							job_title='".encode($d->jobtitle)."', 
							affiliation='".encode($d->affiliation)."', 
							address='".encode($d->address)."', 
							city='".encode($d->city)."', 
							state='".encode($d->state)."', 
							zip_code='".encode($d->zipcode)."', 
							country='".encode($d->country)."', 
							email='".encode($d->email)."', 
							phone1='".encode($d->phone1)."', 
							phone2='".encode($d->phone2)."', 
							fax='".encode($d->fax)."', 
							cellphone='".encode($d->cellphone)."', 
							imported=1, 
							deleted=0, 
							updated_at=NOW(), 
							show_id=".$show_id." 
							WHERE id=".$test['id'],"Update Record");
							
				} else { // Insert New Record
					db_query("INSERT INTO ".$db_table." SET id=".$d['speakerid'].",
															imported=1,
															lkey='".makekey()."',
															show_id=".$show_id.",
															first_name='".encode($d->firstname)."',
															last_name='".encode($d->lastname)."',
															job_title='".encode($d->jobtitle)."',
															affiliation='".encode($d->affiliation)."', 
															address='".encode($d->address)."', 
															city='".encode($d->city)."', 
															state='".encode($d->state)."', 
															zip_code='".encode($d->zipcode)."', 
															country='".encode($d->country)."', 
															email='".encode($d->email)."', 
															phone1='".encode($d->phone1)."', 
															phone2='".encode($d->phone2)."', 
															fax='".encode($d->fax)."', 
															cellphone='".encode($d->cellphone)."',
															created_at=NOW(),updated_at=NOW()
															","Insert Record");
				}
				$cnt++;
			}
			echo 'Complete! (Imported '.$cnt.' records)
			';
			ob_flush();
			flush();
		}

	}
	db_query("UPDATE shows SET last_import_at=now() WHERE id='".$show_id."'","Set Last Updated");
	$end_time = microtime(true);
	
	echo "
	
	Import Complete.. ".(round(($end_time-$begin_time)*1000)/1000)." Seconds Total Import Time
	
	
	";
	ob_flush();
	flush();


function convert_smart_quotes($string) {
  //converts smart quotes to normal quotes.
  $search = array(chr(145), chr(146), chr(147), chr(148), chr(151));
  $replace = array("'", "'", '"', '"', '-');
  return str_replace($search, $replace, $string);
}

function encode($val,$extra="") {
	$val = str_replace("'",'&#39;',stripslashes($val));
	$val = str_replace('"',"&quot;",$val);

	if($extra == "tags") { 
		$val = str_replace("<",'&lt;',stripslashes($val));
		$val = str_replace('>',"&gt;",$val);
	}
	if($extra == "amp") { 
		$val = str_replace("&",'&amp;',stripslashes($val));
	}
	return $val;
}

function quoteencode($val,$extra="") {
	$val = convert_smart_quotes($val);
	$val = htmlentities($val);
	$val = str_replace('&Acirc;','',$val);
	$val = html_entity_decode($val);
	$val = str_replace('"',"&quot;",$val);
	$val = str_replace("'",'&#39;',stripslashes($val));

	if($extra == "tags") { 
		$val = str_replace("<",'&lt;',stripslashes($val));
		$val = str_replace('>',"&gt;",$val);
	}
	if($extra == "amp") { 
		$val = str_replace("&",'&amp;',stripslashes($val));
	}
	return $val;
}


function decode($val,$extra="") {
	$val = str_replace("&amp;",'&',stripslashes($val));
	$val = str_replace('&quot;','"',$val);
	$val = str_replace("&#39;","'",$val);
	
	if($extra == "tags") { 
		$val = str_replace('&lt;',"<",$val);
		$val = str_replace("&gt;",'>',$val);
	}
	if($extra == "amp") { 
		$val = str_replace("&amp;",'&',stripslashes($val));
	}
	return $val;
}

function db_query($query, $description, $fetch=0, $ignore_warnings=false, $connection=null) {
	global $mysqli_connection, $database_time;
	if($connection == null) {
		$connection = $mysqli_connection;
	}

	$result = mysqli_query($connection, $query);

	return(($fetch != 0 ? mysqli_fetch_assoc($result) : $result));
}



?>
