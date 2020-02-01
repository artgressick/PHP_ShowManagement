document.write('<script type="text/javascript" src="'+ BF + 'includes/forms.js"></script>');
var totalErrors = 0;
function error_check() {
	if(totalErrors != 0) { reset_errors(); }  
	
	totalErrors = 0;

	if(errEmpty('first_name', "You must enter a First Name.")) { totalErrors++; }
	if(errEmpty('last_name', "You must enter a Last name.")) { totalErrors++; }
	if(errEmpty('email',"You must enter a E-mail Address.")) { 
		totalErrors++; 
	} else {
		if(errEmail('email','','This is not a valid Email Address.')) { totalErrors++; }
	}
	
	if(errEmpty('group_id', "You must select Group Access")) { totalErrors++; }
	if(page == 'add') {
		if(errPasswordsEmpty('crypted_password','crypted_password2',"You Must Enter a Password")) { totalErrors++; }
		else if (errPasswordsMatch('crypted_password','crypted_password2',"Passwords must match")) { totalErrors++; }
	} else {
		if(errPasswordsMatch('crypted_password','crypted_password2',"Passwords must match")) { totalErrors++; }
	}
	return (totalErrors == 0 ? true : false);
}