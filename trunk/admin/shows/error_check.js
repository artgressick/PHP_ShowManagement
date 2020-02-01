document.write('<script type="text/javascript" src="'+ BF + 'includes/forms.js"></script>');
var totalErrors = 0;
function error_check() {
	if(totalErrors != 0) { reset_errors(); }  
	
	totalErrors = 0;

	if(errEmpty('show_name', "You must enter a Show Name.")) { totalErrors++; }
	if(errEmpty('start_date', "You must enter a Start Date.")) { totalErrors++; }
	if(errEmpty('end_date', "You must enter a End Date.")) { totalErrors++; }
	if(errEmpty('lock_requests', "You must enter a Order Lock Date.")) { totalErrors++; }
	if(errEmpty('show_room_data', "You must enter a Show Room Data Date.")) { totalErrors++; }
	if(errEmpty('show_signoff', "You must enter a Show Sign-off Date.")) { totalErrors++; }
	if(errEmpty('status_id',"You must select a Status.")) { totalErrors++; }

	return (totalErrors == 0 ? true : false);
}