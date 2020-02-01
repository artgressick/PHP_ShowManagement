document.write('<script type="text/javascript" src="'+ BF + 'includes/forms.js"></script>');
var totalErrors = 0;
function error_check() {
	if(totalErrors != 0) { reset_errors(); }  
	
	totalErrors = 0;

	if(errEmpty('room_name', "You must enter a Room Name.")) { totalErrors++; }
	if(errEmpty('room_number', "You must enter a Room Number.")) { totalErrors++; }
	if(errEmpty('building_id', "You must select a Building.")) { totalErrors++; }
	if(errEmpty('platform_id', "You must select a Platform.")) { totalErrors++; }

	return (totalErrors == 0 ? true : false);
}