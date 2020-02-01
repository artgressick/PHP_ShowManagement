<?
	$_SESSION['errorMessages'] = array();

	if (isset($_POST['auth_form_name'])) {  // check to see if this is a submission of the login form
		$auth_form_name = strtolower($_REQUEST['auth_form_name']);

		$q = "SELECT users.*, groups.admin_access, groups.orders_quoted, groups.group_name
			FROM users
			JOIN groups ON users.group_id=groups.id
			WHERE !users.deleted AND users.email='" . $auth_form_name . "'
		";
		$result = db_query($q, "auth_check: verifying Email.");
		
		if (mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
			$pass = sha1($_POST['auth_form_password'].$row['salt']);
			if($pass == $row['crypted_password'] && $row['enabled']) {
							
				# Set the session variables that will be used in the rest of the site
				$_SESSION['user_email'] = $row["email"];
				$_SESSION['user_id'] = $row["id"];
				$_SESSION['first_name'] = $row["first_name"];
				$_SESSION['last_name'] = $row["last_name"];
				$_SESSION['mobile'] = $row["cellnumber"];
				$_SESSION['group_id'] = $row['group_id'];
				$_SESSION['admin_access'] = $row['admin_access'];
				$_SESSION['orders_quoted'] = $row['orders_quoted'];
				$_SESSION['group_name'] = $row['group_name'];
				$_SESSION['logedin_at'] = date('m/d/Y H:m:s');
				$_SESSION['lastsecuritycheck_at'] = date('m/d/Y H:i:s');
				$_SESSION['auto_logged'] = false;
			
				# This resets their login attempts after the total amount of failed attempts was logged
				db_query("set session group_concat_max_len=10120;","Setting Max Group Concat to 10k");
				db_query("UPDATE users SET lastlogin_at=NOW() WHERE id=". $row['id'],'Set last login');

				setcookie("email", $row['email'], time()+60*60*24*180, '/');  /* expire in 180 days */
				$_COOKIE['email'] = $row['email'];

				# This sends the user to whatever page they were originally trying to get to before being stopped to login
				header('Location: '.$BF.'mobile/show.php');
				die();
			} else {
				if($row['enabled']) {
					# If the aacount failed to log in, but is under 5 attempts, show them the generic message and log the attempt
					$_SESSION['errorMessages'][] = "Authentication failed<!--(1)-->.";
				} else {
					$_SESSION['errorMessages'][] = "Authentication failed<!--(2)-->.";
				}
			}
		} else {
			# Nothing came back for this email address in the DB.  Generic message ensues.
			$_SESSION['errorMessages'][] = "Authentication failed<!--(3)-->.";
		}
	
	}

	# if they need to be log in for the current page and currently are not yet logged in, send them to the login page.
	include_once($BF.'components/formfields.php');
	include($BF . "mobile/login.php");
	include($BF ."models/mnonav.php");		
	die();

?>
