<?php


//Auth
//require __DIR__ . '/vendor/autoload.php';


$URL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$parts = parse_url($URL);

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'forum';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);

mysql_select_db($dbname, $conn);

function query($query) {
        global $conn;
        return mysql_query($query, $conn);
}



function getSingle($query) {
        $result = query($query);
        $row = mysql_fetch_row($result);
        return $row[0];
}



print <<<EOF
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<link href="styles/posts.css?<?php echo time(); ?>" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

EOF;

//include('authentication.php');

switch ($parts['path']) {
	case "/post":
		print("hello");
		break;
	case "/feed":
		include('posts.php');
		break;
	case "/signin":
		include('signin.php');
		break;
	case "/createAccount":	
		include('createAccount.php');
		break;
}



?>
