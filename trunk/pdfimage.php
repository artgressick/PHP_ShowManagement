<?php
	$BF = "";
	$NON_HTML_PAGE = true;
	require($BF. '_lib.php');

	$MAGICK_HOME="/usr";
	putenv("MAGICK_HOME=$MAGICK_HOME");
	putenv("PATH=$MAGICK_HOME/bin:/usr/local/bin:".$_SERVER["PATH"]);
	putenv("DYLD_LIBRARY_PATH=$MAGICK_HOME/lib");

	if($_REQUEST['file'] == "") {
		clean_exit();
	} else {
		$pwd = getcwd() ."/files/";
		$output_directory = $BF;

		//$cmd = exec("/$MAGICK_HOME/bin/convert /Users/dnitsch/Sites/svn/showman/trunk/diagrams/". $info['chrName'] ."[0] -resize 600x600 /Users/dnitsch/Sites/svn/showman/trunk/diagrams/x.jpg");
		$cmd = "$MAGICK_HOME/bin/convert ". $pwd . $_REQUEST['file'] . "[0] ". ($_REQUEST['showall'] == 1 ? "" : "-resize 300x ") .$pwd . "x.jpg";
		//dtn: The showall is a request.  You can link directly to this script and it will show the actual 100% of the pdf first page.  
		//dtn: 		ex: <a href='pdfimage.php?id=123&showall=1'><img src='pdfimage.php?id=123&showall=1' alt='' /></a>

		$result = shell_exec($cmd);
			
		if($result == NULL){
			ob_clean(); 
			header('Content-type: image/jpeg');
			echo(file_get_contents($pwd. 'x.jpg'));
			unlink($pwd. 'x.jpg');
			exit;
		}

	}

	function clean_exit()
	{
		$img = imagecreatetruecolor(1, 1);
		$color = imagecolorallocate($img, 255, 255, 255);
		imagesetpixel($img, 100, 100, $color);
		header('Content-type: image/jpeg');
		imagejpeg($img);
		imagedestroy($img); 
		die();
	}
?>