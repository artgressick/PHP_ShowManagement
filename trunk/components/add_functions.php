<?
	//-----------------------------------------------------------------------------------------------
	// New Function designed by Daniel Tisza-Nitsch
	// ** Random key generator.  This was make a rediculously secure key to search for values on.
	//-----------------------------------------------------------------------------------------------
	function makekey() {
		$email = (isset($_SESSION['email']) ? $_SESSION['email'] : 'unknown@emailadsa.com');
	    return sha1(uniqid(mt_rand(1000000,9999999).$email.time(), true));
	}
?>