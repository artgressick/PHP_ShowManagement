document.write('<script type="text/javascript" src="'+ BF + 'includes/forms.js"></script>');
var totalErrors = 0;
function error_check() {
	if(totalErrors != 0) { reset_errors(); }  
	
	totalErrors = 0;

	if(errEmpty('sessioncat_name', "You must enter a Session Category Name.")) { totalErrors++; }

	return (totalErrors == 0 ? true : false);
}