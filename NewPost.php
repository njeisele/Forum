<?php

	function isAllowedToPost($uid) {
        	global $dbConn;
        	/* prevent same user from posting too often */
        	$maxPostsPerMinute = 3;
        	$dateOneMinuteAgo = mysql_real_escape_string(date('Y-m-d H:i:s', strtotime('-1 minute')));
        	$result = $dbConn->getSingle("SELECT COUNT(*) from posts WHERE uid = $uid and date > '$dateOneMinuteAgo'");
        	return ($result <= $maxPostsPerMinute);
	}

	isAllowedToPost(getUid());
        
	function getUid() {
		global $dbConn;
		$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
		$uid = $dbConn->getSingle("select uid from users where ip ='".$ip."'");
		if (!$uid) {
			$dbConn->query("insert into users(ip) values('$ip')");
		}
		return $uid;
	}

	
	if($_REQUEST['post'] && $_REQUEST['postTitle']) {
        	global $dbConn;
        	$post = mysql_real_escape_string($_REQUEST['post']);
		$postTitle = mysql_real_escape_string($_REQUEST['postTitle']);
        	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
        	$uid = $dbConn->getSingle("select uid from users where ip = '".$ip."'");
        	if (!$uid) {
                	/* New user */
                	$dbConn->query("insert into users (ip) values ('$ip')");
        	}
        	$date = Date("Y-m-d H:i:s");
        	if (isAllowedToPost($uid)) {
                	$dbConn->query("insert into posts (uid, post, date, title) values ($uid, '$post', '$date', '$postTitle')");
		print <<<EOF
			<script type="text/javascript">
				window.location.href = '/feed'; 
			</script>  
EOF;
		} else {
		print("You have posted too many times in the past minute, please wait and try again");
		print <<<EOF
			<img src="https://i.pinimg.com/originals/0f/a5/10/0fa510b2f6630a7b80e227d77c7679f3.jpg" />			
EOF;
        	}
	}
	
	print <<<EOF
	 <form action=newPost method='POST'>
	 <table style="margin-bottom: 5vh !important" class="mainContent">
		<tr>
                <td>
                <div class="form-floating">
                <textarea placeholder="meme" style="margin-bottom: 10px; height: 10vh" name=postTitle class="form-control" id="floatingTextArea"></textarea>
		<textarea style="margin-bottom: 50px; height: 30vh" name=post class="form-control"  id="floatingTextArea"></textarea>
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

?>	
