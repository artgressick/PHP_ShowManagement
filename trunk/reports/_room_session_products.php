<?php
	require($BF.'components/fpdf/fpdf.php');

	//Reference goto http://www.builderau.com.au/program/php/soa/Generating-PDF-files-with-PHP-and-FPDF/0,339028448,339286044,00.htm
	
/*	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
*/	
	$q = "SELECT rooms.*, buildings.building_name
		FROM rooms
		JOIN buildings ON rooms.building_id=buildings.id
		JOIN time_slots AS ts ON ts.room_id=rooms.id
		WHERE !rooms.deleted AND !ts.deleted AND ts.start_date = '".$_REQUEST['date']."' AND !buildings.deleted 
			AND rooms.id IN (".implode(',',$_POST['listids']).")
		GROUP BY rooms.id
		ORDER BY room_name";

	$rooms = db_query($q,"getting room data");

	$full_width = '594';
	$half_screen = '297';
	$half_width = '287';

class PDF extends FPDF {
	//Page footer
	function Footer() {
	    //Position at 1.5 cm from bottom
	    $this->SetY(-75);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
	//create a FPDF object
	$pdf=new PDF('P','pt','A4');
	$pdf->AliasNbPages();

	//set document properties
	$pdf->SetAuthor('TechIT Solutions');
	$pdf->SetTitle('NECC 2009 - Session Quote');
	$pdf->SetMargins(1,1,1);
	
	
	while($room_row = mysqli_fetch_assoc($rooms)) {
		//Add a Page
		$pdf->AddPage('P');
		$pdf->SetDisplayMode(real,'default');
		$pdf->SetAutoPageBreak(1);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell($full_width,20,'NECC 2009 - '.date('n/j/Y',strtotime($_POST['date'])).' - Room: '.decode($room_row['room_name']).' - Building: '.decode($room_row['building_name']),'0',2,'C',0);
		$pdf->SetFont('Arial','',8);
		
		$Y1 = $pdf->GetY();
		$pdf->SetXY(10,$Y1);
		$pdf->MultiCell($half_width,12,'Room Description: '.decode($room_row['description']),'1',2,'L',0);
		$Y2 = $pdf->GetY();
		$pdf->SetXY($half_screen,$Y1);
		$pdf->Cell($half_width,($Y2 - $Y1),'Has Internet Access: '.(!$room_row['internet_access']?'NO':'Yes'),'1',0,'L',0);

		$X = 10;
		$Y = $Y2+10;
		if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
		$pdf->SetXY($X,$Y);
		$pdf->SetFillColor(175,175,175);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell($full_width-20,15,'Room Products','1',0,'C',1);
		$pdf->SetFillColor(216,216,216);

		$X = 10;
		$Y = $Y+20;
		$Y_remember = $Y;
		
		//Now lets get the room equipment
		//First Product Types
		$q = "SELECT pt.id, pt.product_type
				FROM room_products AS rp 
				JOIN products AS p ON rp.product_id=p.id
				JOIN product_types AS pt ON p.producttype_id=pt.id
				WHERE !p.deleted AND !pt.deleted AND pt.id != 2 AND rp.room_id = '".$room_row['id']."'
				GROUP BY pt.id
				ORDER BY product_type";
				
		$rpt = db_query($q,'Get Room Product Types');
		
		if(mysqli_num_rows($rpt) > 0) {
		
			$halfpt = ceil(mysqli_num_rows($rpt) / 2);
			
			$cnt = 1; //Indicates Column 1
			$col2 = false;
			unset($Y3);
			while($pt_row = mysqli_fetch_assoc($rpt)) {
				if($cnt > $halfpt && $col2 == false) {
					$Y2_remember = $pdf->GetY();
					$X = $half_screen+10;
					$Y = $Y_remember;
					unset($Y3);
					$col2 = true;
				}

				if(isset($Y3)) {
					$Y += $Y3;
					$pdf->SetXY($X,$Y);
				}
				if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
				$pdf->SetXY($X,$Y);
				$pdf->SetFont('Arial','B',9);
				
				$Y = $pdf->GetY();
				$pdf->Cell($half_width-60,15,decode($pt_row['product_type']).' Products','1',2,'L',1);
				$pdf->SetXY($X+$half_width-60,$Y);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(50,15,'Qty.','T,R,B',2,'L',1);

				$q = "SELECT rp.id, rp.quantity, p.product_name, p.track_product, p.common_name
						FROM room_products AS rp 
						JOIN products AS p ON rp.product_id=p.id
						WHERE !p.deleted AND p.producttype_id = '".$pt_row['id']."' AND rp.room_id = '".$room_row['id']."'
						ORDER BY product_name";
						
				$rp = db_query($q,'Getting Products');
				$Y3 = 15;
				while($rp_row = mysqli_fetch_assoc($rp)) {
					$Y += $Y3;
					if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
					$pdf->SetXY($X,$Y);
					$pdf->SetFont('Arial','',8);
					$Y1 = $pdf->GetY();
					$pdf->SetXY($X,$Y1);
					$pdf->MultiCell($half_width-60,12,decode($rp_row['product_name']).' ('.decode($rp_row['common_name']).')'.($rp_row['track_product']?'*':''),'L,B',2,'L',0);
					$Y2 = $pdf->GetY();
					$pdf->SetXY($X+$half_screen-70,$Y1);
					$pdf->Cell(50,($Y2 - $Y1),$rp_row['quantity'],'L,B,R',0,'L',0);
					$Y3 = $Y2 - $Y1;
				}		
				$cnt++;
			}
		} else {
			//No Room Equipment
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			$pdf->SetFont('Arial','I',8);
			$pdf->Cell($full_width-20,10,'No Products Assigned to this Room','1',0,'C',0);
			$Y2_remember = $Y;
			
		}
		
		$X = 10;
		$Y = $pdf->GetY();
		if($Y < $Y2_remember) { $Y = $Y2_remember; }
		if($Y+20 > 770) { $Y = 5; }
		$pdf->SetXY($X,$Y+20);
		$pdf->SetFont('Arial','B',11);
		//Now for tracked products in this room
		$pdf->Cell($full_width-20,15,'Room Tracked Products','1',0,'C',1);
		$Y = $Y_remember = $pdf->GetY() + 20;
		
		$assets = db_query("SELECT products.id, COUNT(product_tracking.id) AS product_count, CONCAT(products.product_name,' (',products.common_name,')') AS product_name, product_types.product_type
					FROM product_tracking
					JOIN products ON product_tracking.product_id=products.id 
					JOIN product_types ON products.producttype_id=product_types.id
					WHERE product_tracking.show_id='".$_SESSION['show_id']."' AND product_tracking.room_id='".$room_row['id']."' 
						AND product_tracking.check_in IS NULL
					GROUP BY product_tracking.product_id
					ORDER BY product_name
		","Get asset counts grouped by product");
		
		if(mysqli_num_rows($assets) > 0) {
			$halfpt = ceil(mysqli_num_rows($assets) / 2);
			
			$cnt = 1; //Indicates Column 1
			$pdf->SetFont('Arial','',8);
			$col2 = false;
			unset($Y3);
			while($asset_row = mysqli_fetch_assoc($assets)) {

				if($cnt > $halfpt && $col2 == false) {
					$Y2_remember = $pdf->GetY();
					$X = $half_screen+10;
					$Y = $Y_remember;
					$col2 = true;
				}

				if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
				$pdf->SetXY($X,$Y);
				$Y1 = $pdf->GetY();
				$pdf->MultiCell($half_width-60,12,decode($asset_row['product_name']).' - '.decode($asset_row['product_type']),'1',2,'L',0);
				$Y2 = $pdf->GetY();
				$pdf->SetXY($X+$half_screen-70,$Y1);
				$pdf->Cell(50,($Y2 - $Y1),$asset_row['product_count'],'1',0,'L',0);
				$Y = $Y + ($Y2 - $Y1);
				$cnt++;
			}
		} else {
			//No Room Equipment
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			$pdf->SetFont('Arial','I',8);
			$pdf->Cell($full_width-20,10,'No Products Checked into this Room','1',0,'C',0);
			$Y2_remember = $Y + 10;
		}

		$pdf->SetXY($X,$Y);
		$X = 10;
		$Y = $pdf->GetY();
		if($Y < $Y2_remember) { $Y = $Y2_remember; }
		$Y += 5;
		if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','B',12);
		$pdf->SetFillColor(175,175,175);
		$pdf->Cell($full_width-20,15,'Sessions','1',0,'C',1);
		$pdf->SetFillColor(216,216,216);
		$q = "SELECT ts.*, c.class_name, c.speaker, st.sessiontype_name
				FROM time_slots AS ts
				JOIN classes AS c ON ts.class_id=c.id
				JOIN sessiontypes AS st ON c.sessiontype_id=st.id
				WHERE !ts.deleted AND !c.deleted AND !st.deleted AND ts.start_date='".$_POST['date']."' AND c.show_id = '".$_SESSION['show_id']."' AND ts.room_id='".$room_row['id']."'
				ORDER BY ts.start_time, ts.end_time, ts.prep_time
		";
		$sessions = db_query($q,'Getting Sessions for Room');
		$count = 1;
		
		while($s_row = mysqli_fetch_assoc($sessions)) {
			$Y += 20;
			if($count++ > 1) {
				if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
				$pdf->SetXY($X,$Y);
				$pdf->SetFillColor(0,0,0);
				$pdf->Cell($full_width-20,5,'','1',0,'L',1);
				$pdf->SetFillColor(216,216,216);
				$Y += 15;
			}
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell($full_width-20,15,decode($s_row['class_name']).' ('.decode($s_row['sessiontype_name']).')','1',0,'L',1);
			$Y += 15;
			$pdf->SetFont('Arial','',8);
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			$pdf->Cell($half_width,15,'Date: '.date('n/j/Y',strtotime($s_row['start_date'])),'L',0,'L',0);
			$pdf->SetXY($X+$half_width,$Y);
			$pdf->Cell($half_width,15,'Speaker: '.decode($s_row['speaker']),'R',0,'L',0);
			$Y += 15;
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			$pdf->Cell($half_width,15,'Prep Time: '.date('g:i a',strtotime($s_row['prep_time'])),'L',0,'L',0);
			$pdf->SetXY($X+$half_width,$Y);
			$pdf->Cell($half_width,15,'Start Time: '.date('g:i a',strtotime($s_row['start_time'])),'R',0,'L',0);
			$Y += 15;
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			$pdf->Cell($half_width,15,'End Time: '.date('g:i a',strtotime($s_row['end_time'])),'L,B',0,'L',0);
			$pdf->SetXY($X+$half_width,$Y);
			$pdf->Cell($half_width,15,'Strike Time: '.date('g:i a',strtotime($s_row['strike_time'])),'R,B',0,'L',0);
			$Y += 20;
			if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
			$pdf->SetXY($X,$Y);
			
			$spt = db_query("SELECT pt.id, pt.product_type
								FROM session_order_items AS soi
								JOIN products AS p ON soi.product_id=p.id
								JOIN product_types AS pt ON p.producttype_id=pt.id
								JOIN time_slots AS ts ON soi.timeslot_id=ts.id
								JOIN classes AS c ON ts.class_id=c.id
								JOIN session_orders AS so ON so.session_id=c.id
								WHERE !p.deleted AND !pt.deleted AND so.status_id=2 AND soi.approved AND ts.id = '".$s_row['id']."' 
									AND ts.start_date='".$_POST['date']."' AND c.show_id = '".$_SESSION['show_id']."' 
									AND ts.room_id='".$room_row['id']."'
								GROUP BY pt.id
								ORDER BY pt.product_type
			",'Getting Session Product Types');
			$Y_remember = $Y;
			if(mysqli_num_rows($spt) > 0) {
			
				$halfpt = ceil(mysqli_num_rows($spt) / 2);
				
				$cnt = 1; //Indicates Column 1
				$col2 = false;
				unset($Y3);

				while($st_row = mysqli_fetch_assoc($spt)) {
					if($cnt > $halfpt) {
						$Y2_remember = $Y;
						$X = $half_screen+10;
						$Y = $Y_remember;
						unset($Y3);
						$col2 = true;
					}
	
					if(isset($Y3)) {
						$Y += $Y3;
						$pdf->SetXY($X,$Y);
					}
					if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
					$pdf->SetXY($X,$Y);
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell($half_width-60,15,decode($st_row['product_type']).' Products','1',2,'L',1);
					if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
					$pdf->SetXY($X+$half_width-60,$Y);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(50,15,'Qty.','T,R,B',2,'L',1);
	
					$q = "SELECT soi.id, soi.quantity, p.product_name, p.track_product, p.common_name
							FROM session_order_items AS soi
							JOIN products AS p ON soi.product_id=p.id
							JOIN time_slots AS ts ON soi.timeslot_id=ts.id
							JOIN classes AS c ON ts.class_id=c.id
							JOIN session_orders AS so ON so.session_id=c.id
							WHERE !p.deleted AND p.producttype_id = '".$st_row['id']."' AND ts.id = '".$s_row['id']."' AND so.status_id=2 
								AND soi.approved AND ts.start_date='".$_POST['date']."' AND c.show_id = '".$_SESSION['show_id']."' 
								AND ts.room_id='".$room_row['id']."'
							ORDER BY product_name
							";
							
					$sp = db_query($q,'Getting Products');
					$Y3 = 15;
					while($sp_row = mysqli_fetch_assoc($sp)) {
						$Y += $Y3;
						if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
						$pdf->SetXY($X,$Y);
						$pdf->SetFont('Arial','',8);
						$Y1 = $pdf->GetY();
						$pdf->SetXY($X,$Y1);
						$pdf->MultiCell($half_width-60,12,decode($sp_row['product_name']).' ('.decode($sp_row['common_name']).')'.($sp_row['track_product']?'*':''),'L,B',2,'L',0);
						$Y2 = $pdf->GetY();
						$pdf->SetXY($X+$half_screen-70,$Y1);
						$pdf->Cell(50,($Y2 - $Y1),$sp_row['quantity'],'L,B,R',0,'L',0);
						$Y3 = $Y2 - $Y1;
					}		
					$cnt++;
				}
			} else {
				//No Room Equipment
				if($Y > 750) { $pdf->AddPage('P'); $Y = 5; }
				$pdf->SetXY($X,$Y);
				$pdf->SetFont('Arial','I',8);
				$pdf->Cell($full_width-20,10,'No Products Assigned to this Session','1',0,'C',0);
				$Y2_remember = $Y;
				
			}
			$X = 10;
			$Y = $pdf->GetY();
			if($Y < $Y2_remember) { $Y = $Y2_remember; }
		}

	}

	
	//Output the document
	$pdf->Output('Room-Session-Products-'.$_POST['date'].'.pdf','I');
	
	die();
?>