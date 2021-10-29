<?php


function isAllowedToPost($uid) {
	/* prevent same user from posting too often */
	$maxPostsPerMinute = 3;
	$dateOneMinuteAgo = mysql_real_escape_string(date('Y-m-d H:i:s', strtotime('-1 minute')));
	$result = getSingle("SELECT COUNT(*) from posts WHERE uid = $uid and date > '$dateOneMinuteAgo'");
	return ($result <= $maxPostsPerMinute);
}

isAllowedToPost(2);


if($_REQUEST['post']) {
	$post = mysql_real_escape_string($_REQUEST['post']);
	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
	$uid = getSingle("select uid from users where ip = '".$ip."'");
	if (!$uid) {
		/* New user */
		query("insert into users (ip) values ('$ip')");
	}
	$date = Date("Y-m-d H:i:s");
	if (isAllowedToPost($uid)) {
		query("insert into posts (uid, post, date) values ($uid, '$post', '$date')");  	
	} else {
		print("You have posted too many times in the past minute, please wait and try again");
	}
}

function getUid() {
	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
	$uid = getSingle("select uid from users where ip ='".$ip."'");
	if (!$uid) {
		query("insert into users(ip) values('$ip')");
	}
	return $uid;
}

if ($_REQUEST['vote'] && $_REQUEST['pid']) {
	$vote = $_REQUEST['vote'];
	$pid = $_REQUEST['pid'];
        $uid = getUid();
	query("insert into votes(uid, pid, status) values($uid, $pid, '$vote')");
	// Update in case already exists
	query("update votes SET status='$vote' WHERE uid = $uid and pid = $pid");		
}

/* styles */
print <<<EOF
<body style="background-color: #BBBBBB">	
	<form action=feed method='POST'>
	<table style="margin-bottom: 5vh !important" class="mainContent">
	<tr>
		<td>
		<div class="form-floating">
		<textarea style="margin-bottom: 50px; height: 30vh" name=post class="form-control" id="floatingTextArea"></textarea>
		</div>
		</td>
	</tr>
	<tr>
	<td>
	<button class="btn btn-primary" type="submit">Post</button>
	</td>
	</tr>
	</table>
	</form>
EOF;

$result = query("select * from posts order by date desc LIMIT 10");
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
