<?
	#This is the controller for the entire site and has information for the folder similar to the Ruby setup

//This is the base folder for all pages in this directory
	$BF="";	

//This is the controller for the pages in this folder
switch ($page_nav) {
case "index":
	$page_title="Staffing Dashboard";
	$content_page="login.php";
	include($BF .'models/template.php');
	break;

case "login_page":
	$page_title="Create a Profile";
	break;

case "create_profile":
	$page_title="Create a Profile";
	break;
}
?>