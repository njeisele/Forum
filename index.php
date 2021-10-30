<?php


//Auth
//require __DIR__ . '/vendor/autoload.php';


$URL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$parts = parse_url($URL);

require("Controllers/Mysql.php");

$dbConn = new Mysql();
$dbConn->dbConnect();


print <<<EOF
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<link href="styles/posts.css?<?php echo time(); ?>" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

EOF;

//include('authentication.php');
$parts = explode("/", $parts['path']);
switch ($parts[1]) {
	case "post":
		include("Views/post.php");
		break;
	case "feed":
		include('Views/posts.php');
		break;
	case "signin":
		include('Views/signin.php');
		break;
	case "createAccount":	
		include('Views/createAccount.php');
		break;
	case "newPost":
		include('Views/NewPost.php');
		break;
}



?>
