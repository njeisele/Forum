<?php

// TODO: this is re-used in new post, should be moved elsewhere
/*function getUid() {
	global $dbConn;
	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
	$uid = $dbConn->getSingle("select uid from users where ip ='".$ip."'");
	if (!$uid) {
		$dbConn->query("insert into users(ip) values('$ip')");
	}
	return $uid;
}*/


// TODO: Move this vote logic to the replies page

/*
if ($_REQUEST['vote'] && $_REQUEST['pid']) {
	global $dbConn;
	$vote = $_REQUEST['vote'];
	$pid = $_REQUEST['pid'];
        $uid = getUid();
	$dbConn->query("insert into votes(uid, pid, status) values($uid, $pid, '$vote')");
	// Update in case already exists
	$dbConn->query("update votes SET status='$vote' WHERE uid = $uid and pid = $pid");		
}*/

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


$result = $dbConn->query("select * from posts WHERE isTopic = 1 order by date desc LIMIT 10");
print "<div class=\"feed mainContent\">";
while ($row = mysql_fetch_assoc($result)) {
	$uid = $row['username'];
	$pid = $row['pid'];
	$title = $row['title'];
	$post = htmlspecialchars($row['text']);
	$date = $row['date'];
	$likeString = 'like';
	$dislikeString = 'dislike';
	/*$dislike = <<<EOF
        
	<a href=/feed?vote=$dislikeString&pid=$pid>Dislike</a>
EOF;

	$like = <<<EOF
	<a href=/feed?vote=$likeString&pid=$pid>Like</a>
EOF;
*/

	print <<<EOF
	<a href="post/$pid" style="color: black; text-decoration: none">
	 		<div class="post">
			<div style="width: 100%; display: inline"> <h6 style="display: inline-block; float: left; margin-left: 10px; margin-top: 10px;">$title</h6> <p style="float: right; display: inline-block; margin-right: 10px; margin-top: 10px">$date</p></div>
                        <p style="padding-top: 40px; margin-left: 10px"> $post </p>
        	</div></a>

EOF;
}

print "</div></body>"

?>
