<?php
	$NON_HTML_PAGE = true;
	include('_controller.php');

	require($BF.'components/fpdf/fpdf.php');

	//Reference goto http://www.builderau.com.au/program/php/soa/Generating-PDF-files-with-PHP-and-FPDF/0,339028448,339286044,00.htm

	//create a FPDF object
	$pdf=new FPDF('P','pt','A4');
	
	//set document properties
	$pdf->SetAuthor('TechIT Solutions');
	$pdf->SetTitle('NECC 2009 - Session Quote');
	
	//Add a Page
	$pdf->AddPage('P');
	$pdf->SetDisplayMode(real,'continuous');
	$pdf->SetAutoPageBreak(1);
	
	//Logo and link to our company
	$pdf->Image($BF.'images/techit.jpg',430,20,150,0,'JPG','http://www.techitsolutions.com/');
	
	//display the title with a border around it
	$pdf->SetFont('Arial','B',12);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetXY(20,20);
	$pdf->SetDrawColor(0,0,0);
	$pdf->Cell(400,25,'NECC 2009 - Session Quote','L,T,R',2,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(400,12,'Job Code: '.$orderinfo['quote_name'].'-'.$orderinfo['revision'],'L,R',2,'L',0);
	$pdf->Cell(400,12,'Show: '.decode($_SESSION['show_name']),'L,R',2,'L',0);
	$pdf->MultiCell(400,12,'Session: '.decode($info['class_name']),'L,R',2,'L');
	$pdf->Cell(400,12,'Client/Company: '.decode($info['bill_name']).' ('.decode($info['bill_email']).')','L,R',2,'L',0,'mailto:'.$info['bill_email']);
	$pdf->Cell(400,12,'Phone #: '.$info['bill_phone'],'L,R',2,'L',0);
	$pdf->Cell(400,12,'Quote Version: '.$orderinfo['revision'].'/'.date('Y-m-d',strtotime($orderinfo['updated_at'])).'/SM','L,R,B',2,'L',0);

	//Now for the Query
	$q = "SELECT TS.id, TS.start_date, TS.start_time, TS.end_time, TS.description AS tsdescription, R.room_number, R.description, B.building_name, PT.id AS pt_id, PT.product_type, P.product_name, P.common_name, SOI.quantity, SOI.price, SOI.setup
	
			FROM product_types AS PT
			JOIN products AS P ON P.producttype_id=PT.id
			JOIN session_order_items AS SOI ON SOI.product_id=P.id AND SOI.session_id='".$info['id']."'
			JOIN time_slots AS TS ON SOI.timeslot_id=TS.id
			JOIN rooms as R ON TS.room_id=R.id
			JOIN buildings AS B ON R.building_id=B.id
			
			WHERE !R.deleted AND !PT.deleted AND PT.enabled AND !P.deleted AND P.enabled AND !SOI.deleted AND SOI.approved
			
			ORDER BY TS.start_date, TS.start_time, TS.end_time, PT.product_type, P.product_name, P.common_name
		";
	
	$products = db_query($q,"Get Products");
	
	//set values to blank
	$total = array('product'=>0,'setup'=>0);
	$tsid = '';
	while($row = mysqli_fetch_assoc($products)) {
		if($tsid != $row['id']) {
			$Y = $pdf->GetY();
			$Y += 15;
			$pdf->SetXY(20,$Y);

			$pdf->SetFont('Arial','B',10);
			$pdf->SetFillColor(194,194,194);
			$pdf->Cell(555,20,date('l, F j Y',strtotime($row['start_date'])).' from '.pretty_time($row['start_time']).' to '.pretty_time($row['end_time']).(strlen($row['tsdescription']) > 2 ?' ('.decode($row['tsdescription']).')':''),'L,T,R',2,'L',1);
			$pdf->MultiCell(555,20,'Room: '.decode($row['room_number']).' ('.decode($row['description']).') - Building: '.decode($row['building_name']),'L,T,R',2,'L',1);
		
			$tsid = $row['id'];
			$ptid = '';
		}

		if($ptid != $row['pt_id']) {
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(216,216,216);
			$Y1 = $pdf->GetY();
			$pdf->SetXY(20,$Y1);
			$pdf->Cell(355,15,decode($row['product_type']).' Products','L,T,B',0,'L',1);
			$pdf->Cell(50,15,'Qty.','L,T,B',0,'C',1);
			$pdf->Cell(50,15,'Price','L,T,B',0,'C',1);
			$pdf->Cell(50,15,'Setup','L,T,B',0,'C',1);
			$pdf->Cell(50,15,'Total','L,T,B,R',2,'C',1);
			$ptid = $row['pt_id'];
		}
		$pdf->SetFont('Arial','',8);
		$Y1 = $pdf->GetY();
		$pdf->SetXY(20,$Y1);
		$pdf->MultiCell(355,12,decode($row['product_name']).' ('.decode($row['common_name']).')','L,B',2,'L',0);
		$Y2 = $pdf->GetY();
		$pdf->SetXY(375,$Y1);
		$pdf->Cell(50,($Y2 - $Y1),$row['quantity'],'L,B',0,'C',0);
		$pdf->Cell(50,($Y2 - $Y1),'$'.number_format($row['price'],2,'.',','),'L,B',0,'C',0);
		$pdf->Cell(50,($Y2 - $Y1),'$'.number_format($row['setup'],2,'.',','),'L,B',0,'C',0);
		$pdf->Cell(50,($Y2 - $Y1),'$'.number_format(($row['quantity'] * ($row['price'] + $row['setup'])),2,'.',','),'L,B,R',2,'R',1);
		$total['product'] += ($row['quantity'] * $row['price']);
		$total['setup'] += ($row['quantity'] * $row['setup']);
	} 
	$Y = $pdf->GetY();
	$Y += 15;
	$pdf->SetXY(20,$Y);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(216,216,216);
	$pdf->Cell(455,15,'Summary','L,T,B',0,'L',1);
	$pdf->Cell(100,15,'Total','L,T,B,R',2,'C',1);
	$pdf->SetX(20);
	$pdf->Cell(455,15,'Products','L,B',0,'L',0);
	$pdf->Cell(100,15,'$'.number_format($total['product'],2,'.',','),'L,B,R',2,'R',1);	
	$pdf->SetX(20);
	$pdf->Cell(455,15,'Setup','L,B',0,'L',0);
	$pdf->Cell(100,15,'$'.number_format($total['setup'],2,'.',','),'L,B,R',2,'R',1);	
	$pdf->SetX(20);
	$pdf->Cell(455,15,'Contingency ('.$orderinfo['contingency'].'%)','L,B',0,'L',0);
	$cont = (($orderinfo['contingency'] / 100) * ($total['product'] + $total['setup']));
	$pdf->Cell(100,15,'$'.number_format($cont,2,'.',','),'L,B,R',2,'R',1);	
	$pdf->SetFont('Arial','B',10);
	$pdf->SetX(20);
	$pdf->Cell(455,20,'Order Total','L,B',0,'R',0);
	$pdf->Cell(100,20,'$'.number_format(($total['product'] + $total['setup'] + $cont),2,'.',','),'L,B,R',2,'R',1);	

	$Y = $pdf->GetY();
	$Y += 25;
	$pdf->SetXY(20,$Y);
	$pdf->SetFont('Arial','I',8);
	$pdf->MultiCell(555,12,'This quote is based on information provided at this time.  Any revisions required at a later date will be subject to price review at that time.  We reserve the right to withdraw this quote if it is not accepted within 30 days.  Cancellation fee will apply to confirmed orders.  Thank you for giving us this opportunity.  We look forward to hearing from you.',0,2,'L',0);
	
	$Y = $pdf->GetY();
	$Y += 25;
	$pdf->SetXY(20,$Y);
	$pdf->SetFont('Arial','BI',10);
	$pdf->MultiCell(555,20,'Approved',0,2,'L',0);
	$Y = $pdf->GetY();
	$Y += 20;
	$pdf->SetXY(20,$Y);
	$pdf->MultiCell(300,12,'','B',0,'L',0);
	$pdf->SetXY(320,$Y);
	$pdf->MultiCell(55,12,'Date',0,0,'C',0);
	$pdf->SetXY(375,$Y);
	$pdf->MultiCell(200,12,'','B',0,'L',0);

	$Y = $pdf->GetY();
	$pdf->SetXY(20,$Y);	
	$pdf->SetFont('Arial','BI',9);
	$pdf->Cell(555,20,'Please sign and return via fax 614-340-7190.',0,2,'L',0);
	
	//Output the document
	$pdf->Output($orderinfo['quote_name'].'-'.$orderinfo['revision'].'.pdf',($_REQUEST['action']=='email'?'F':'I'));
	
	if($_REQUEST['action']=='email') {
		require_once($BF.'includes/_emailer.php');
		$to = $info['bill_email'];
//		$to = 'operations@techitsolutions.com';
		$subject = 'NECC Quote: '.$orderinfo['quote_name'].'-'.$orderinfo['revision'];
		$message = 'Here is your NECC 2009 Quote.<br />
		
		';
		$cc = 'operations@techitsolutions.com';
//		$cc='';
		$attachment = array($BF.'admin/orderfulfill/'.$orderinfo['quote_name'].'-'.$orderinfo['revision'].'.pdf');
		if($to != "" && emailer($to,$subject,$message,'',$cc,'','en','UTF-8',$attachment)) {
			$_SESSION['infoMessages'][] = "Order E-mailed to: ".$info['bill_email'];
			unlink($orderinfo['quote_name'].'-'.$orderinfo['revision'].'.pdf');
			header("Location: order.php?key=".$info['lkey']);
			die();
		} else {
			$_SESSION['errorMessages'][] = "Order Was not able to be e-mailed to: (".$info['bill_email'].')';
			unlink($orderinfo['quote_name'].'-'.$orderinfo['revision'].'.pdf');
			header("Location: order.php?key=".$info['lkey']);
			die();
		
		}
	}
?>