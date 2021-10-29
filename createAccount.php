<?php

include("lib/password.php");

function validatePasswords($pass1, $pass2) {
	$pass1Length = strlen($pass1);
	$pass2Length = strlen($pass2);
	if ($pass1Length == null || $pass2Length == null) {
		return false;
	}
	if (preg_match('/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/', $pass1)) {
		return strcmp($pass1, $pass2) == 0;
        }
	return false;
}

if ($_REQUEST['username'] && $_REQUEST['password'] && $_REQUEST['password2']) {
        $username = mysql_real_escape_string($_REQUEST['username']);
        $pass = mysql_real_escape_string($_REQUEST['password']);
	$pass2 = mysql_real_escape_string($_REQUEST['password2']);
	
	$userExists = getSingle("SELECT COUNT(*) FROM passwords WHERE uid='$username'");
	$userLength = strlen($username);
	if ($userExists > 0) {
		print("Username unavailable");
	} else if ($userLength == null || $userLength < 3 || $userLength > 20) {
		print("Invalid username length");
	}	
	$isValid = validatePasswords($pass, $pass2);		
	if (!$isValid) {
		print("Invalid password");
	}
	$hash = password_hash($pass, PASSWORD_DEFAULT);
	query("INSERT INTO passwords (username, password) values ('$username', '$hash')");
}



print <<<EOF
<section class="vh-100">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 text-black">

        <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

          <form action=createAccount method='POST' style="width: 23rem;">

            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Create Account</h3>

            <div class="form-outline mb-4">
              <input name="username" id="form2Example18" class="form-control form-control-lg" />
              <label class="form-label" for="form2Example18">Username</label>
            </div>

            <div class="form-outline mb-4">
              <input type="password" name="password" id="form2Example28" class="form-control form-control-lg" />
              <label class="form-label" for="form2Example28">
		Password (must be 8-20 characters and include a letter, uppercase letter, number, and one of the following
		special characters: !@#$%^&*-)
	      </label>
            </div>

	    <div class="form-outline mb-4">
              <input type="password" name="password2" id="form2Example28" class="form-control form-control-lg" />
              <label class="form-label" for="form2Example28">Re-enter password</label>
            </div>
	

            <div class="pt-1 mb-4">
              <button class="btn btn-info btn-lg btn-block" type="submit">Create Account</button>
            </div>


          </form>

        </div>

      </div>
      <div class="col-sm-6 px-0 d-none d-sm-block">
        <img src="https://cdn.mos.cms.futurecdn.net/3Q7LWsvZRiqc8iMYzvTu5L.jpg" alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: center;">
      </div>
    </div>
  </div>
</section>	
EOF;

?>

