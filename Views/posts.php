<?php

// TODO: this is re-used in new post, should be moved elsewhere
function getUid() {
	global $dbConn;
	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
	$uid = $dbConn->getSingle("select uid from users where ip ='".$ip."'");
	if (!$uid) {
		$dbConn->query("insert into users(ip) values('$ip')");
	}
	return $uid;
}

if ($_REQUEST['vote'] && $_REQUEST['pid']) {
	global $dbConn;
	$vote = $_REQUEST['vote'];
	$pid = $_REQUEST['pid'];
        $uid = getUid();
	$dbConn->query("insert into votes(uid, pid, status) values($uid, $pid, '$vote')");
	// Update in case already exists
	$dbConn->query("update votes SET status='$vote' WHERE uid = $uid and pid = $pid");		
}

/* styles */
print <<<EOF
<body style="background-color: #DDDDDD">	
	<div style="width: 100%; text-align: center; justify-content: center;">
	<a href="/newPost">New post</a>
	</div>
EOF;

$result = $dbConn->query("select * from posts order by date desc LIMIT 10");
print "<div class=\"feed mainContent\">";
while ($row = mysql_fetch_assoc($result)) {
	$uid = $row['uid'];
	$pid = $row['pid'];
	$post = htmlspecialchars($row['post']);
	$date = $row['date'];
	$likeString = 'like';
	$dislikeString = 'dislike';
	$dislike = <<<EOF
        <a href=/feed?vote=$dislikeString&pid=$pid>Dislike</a>
EOF;

	$like = <<<EOF
	<a href=/feed?vote=$likeString&pid=$pid>Like</a>
EOF;


	print <<<EOF
	<div class="post">$post $date $like $dislike</div>		
EOF;
}

print "</div></body>"

?>
