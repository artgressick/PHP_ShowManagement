<?php
	function emailer($to,$subject,$message,$from='operations@techitsolutions.com',$cc='',$bcc='',$mb_language='en',$mb_convert_encoding='UTF-8',$attachments=array()) {
		if($from == '') { $from = 'techIT Solutions Operations <operations@techitsolutions.com>'; }
		//dtn: This is added so that the Pear module can differentiate between HTML emails and plain text emails.
		$er = error_reporting(0); 		//dtn: This is added in so that we don't get spammed with PEAR::isError() messages in our tail -f ..
		include_once('Mail.php');
		include_once('Mail/mime.php');
	
		$crlf = "\n";
		mb_language($mb_language);
		mb_internal_encoding('UTF-8');
		
		$mime = new Mail_mime($crlf);	
	
		$subject = decode($subject);
		$subject = mb_convert_encoding($subject, $mb_convert_encoding,"AUTO");
		$subject = mb_encode_mimeheader($subject);
	
		// removed this for now: 'Bcc'		=> $bcc,
		
		$hdrs = array('From'    => $from,
					  'Cc'		=> $cc,
					  'Subject' => $subject
				  );
		
		$mime->_build_params['text_encoding'] ='quoted-printable';
		$mime->_build_params['text_charset'] = "UTF-8";
		$mime->_build_params['html_charset'] = "UTF-8";
	
		$Message = decode($message);
			
		$mime->setHTMLBody($Message);
		if(count($attachments)) { 
			foreach($attachments AS $k => $file) {
				$mime->addAttachment($file);
			} 
		}
			
		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);
		$body = mb_convert_encoding($body, $mb_convert_encoding, "UTF-8"); 
		
		$mail =& Mail::factory('mail');
		if($mail->send(decode($to), $hdrs, $body)) { return true; } else { return false; }
//		if($mail->send("programmers@techitsolutions.com,storeops@apple.com", $hdrs, $body)) { return true; } else { return false; } // Testing Address
//		return true;
	}
?>