document.write('<script type="text/javascript" src="'+ BF + 'includes/forms.js"></script>');
var totalErrors = 0;
function error_check() {
	if(totalErrors != 0) { reset_errors(); }  
	
	totalErrors = 0;

	if(errEmpty('product_name', "You must enter a Product Name.")) { totalErrors++; }
	if(errEmpty('producttype_id', "You must select a Product Type.")) { totalErrors++; }
	if(errEmpty('vendor_id', "You must select a Vendor.")) { totalErrors++; }

	return (totalErrors == 0 ? true : false);
}