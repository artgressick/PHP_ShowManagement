<?
###############################################################################################################
# miniPopup -- A function to create mini javascript popups.  Useful for onclick and hover over iframe pages
#				All you need to add on the page (as well as including this file...) if an iFrame with
#				an id tag of 'miniPopup'. NOTE:  position has to be set to absolute.
#
# Example: <iframe id='miniPopup' style='position: absolute; display: none; background: #ccc; border: 1px solid gray;'></iframe>
#
# Example:
#	miniPopup(
#			'iframepage.php?id=5',	# This is the actual php page that we will display
#			'ID # 5',				# This is what gets displayed to the user to click, or mouseover or whatever.  Image can go here.
#			'mouseover'				# OPTIONAL -- default is "onclick";  You can add a mouseover which auto adds the mouseout
#	)
###############################################################################################################

	function miniPopup($page,$display,$type='click') {
		if($type == 'click') { $str = "<span onclick='miniPopup(this,event,\"". $page ."\")'>".$display."</span>"; }
		  else if($type == 'mouseover') { $str = "<span onmouseover='miniPopup(this,event,\"". $page ."\")' onmouseout='miniPopup()'>".$display."</span>"; }
		return $str;
	}
?>
<script type='text/javascript'>
var objPage;
function miniPopup(obj,e,page) {
	var mp = document.getElementById('miniPopupWindow');
	if(e) { 
		if(e.type == 'click' || e.type == 'mouseover') { 
		
			if(objPage == page && mp.style.display == '') { miniPopupShrink(mp); return; }
		
			mp.src = page;
			var x = findPos(obj);
			objPage = page;
			mp.style.left = x[0]+'px';
			mp.style.top = x[1]+obj.offsetHeight+'px';
			
			mp.style.display = ''; 
		} else if(e.type == 'mouseout') { 
			miniPopupShrink(mp);
		}
	} else {
		miniPopupShrink(mp);
	}
}
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}
function miniPopupShrink(mp) {
	mp.style.display = 'none';
}
</script>