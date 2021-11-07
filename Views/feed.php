<?php

/* styles */
print <<<EOF
<body style="background-color: #DDDDDD">	
EOF;

include("header.php");

print <<<EOF
	<div style="width: 100%; text-align: center; justify-content: center;">
	<a href="/newPost" style="margin-top: 40px" type="button" class="btn btn-dark">New Post</a>
	</div>
EOF;


print <<<EOF
	<div style="margin-left: 10vw; margin-bottom: 10px;">
		<a class="btn btn-danger" href="/feed?sort=hot"> Hot </a>
		<a class="btn btn-primary" href = "/feed?sort=new"> New </a>
	</div>
EOF;

$result;

if (isset($_REQUEST['sort'])) {
	$sort = $_REQUEST['sort'];
	if ($sort == "hot") {
		
		$result = $dbConn->query("select * from
		 ((select p1.pid as pid, p1.username as username, p1.isTopic as isTopic,
		  p1.title as title, p1.date as date, p1.text as text		
, COUNT(p1.pid) as c from posts as p1 INNER JOIN posts as p2 ON p1.pid = p2.opid
		 GROUP BY p1.pid 
		 ORDER BY c DESC) AS hotPosts )
		 LIMIT 10");
	} else if ($sort = "new") {
		$result = $dbConn->query("select * from posts WHERE isTopic = 1 order by date desc LIMIT 10");	
	} else {
		// default  hot
		$result = $dbConn->query("select * from posts WHERE isTopic = 1 order by date desc LIMIT 10");

	}
} else {
	// default hot
	$result = $dbConn->query("select * from posts WHERE isTopic = 1 order by date desc LIMIT 10");
}

print "<div class=\"feed mainContent\">";
while ($row = mysql_fetch_assoc($result)) {
	$uid = $row['username'];
	$pid = $row['pid'];
	$title = $row['title'];
	$post = htmlspecialchars($row['text']);
	$date = $row['date'];

	print <<<EOF
	<a href="post/$pid" style="color: black; text-decoration: none">
	 		<div class="post">
			<div style="width: 100%; display: inline"> <h6 style="display: inline-block; float: left; margin-left: 10px; margin-top: 10px;">$title</h6> <p style="float: right; display: inline-block; margin-right: 10px; margin-top: 10px">$date</p></div>
                        <p style="padding-top: 40px; overflow-wrap: anywhere; margin-left: 10px"> $post </p>
        	</div></a>

EOF;
}

print "</div></body>"

?>
