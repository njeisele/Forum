<?php

	include("Views/header.php");
	require("authentication.php");
	
	// Check if this username or IP has exceeded the post limite
	function isAllowedToPost($uid, $username) {
        	global $dbConn;
        	/* prevent same user from posting too often */
        	$maxPostsPerMinute = -1;
        	$dateOneMinuteAgo = mysql_real_escape_string(date('Y-m-d H:i:s', strtotime('-1 minute')));
		$result = $dbConn->query("SELECT COUNT(*) from posts WHERE (username = '$username' OR uid = $uid) and date > '$dateOneMinuteAgo'");
                $count = mysql_fetch_row($result)[0];
                return ($count <= $maxPostsPerMinute);
	}

	//isAllowedToPost(getUid());
        
	// TODO: Just going to rate-limit by username for now, but it prob would also be good to do this by IP
	// so they can't make a bunch of accounts from one IP
	
	
	function getUid() {
		global $dbConn;
		$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
		$uid = $dbConn->getSingle("select uid from users where ip ='".$ip."'");
		if (!$uid) {
			$dbConn->query("insert into users(ip) values('$ip')");
		}
		return $uid;
	}
	
	print <<<EOF
	<body style="background-color: #DDDDDD">
EOF;
	
	if(isset($_REQUEST['post']) && isset($_REQUEST['postTitle']) && isset($_REQUEST['token'])) {
        	global $dbConn;
		$auth = new Auth();
                $decodedToken = $auth->verifyToken($_REQUEST['token']);
                $username = mysql_real_escape_string($decodedToken->data->username);
        	
		$post = mysql_real_escape_string($_REQUEST['post']);
		$postTitle = mysql_real_escape_string($_REQUEST['postTitle']);
        	$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
        	$uid = $dbConn->getSingle("select uid from users where ip = '".$ip."'");
        	if (!$uid) {
             		// New IP
                	$dbConn->query("insert into users (ip) values ('$ip')");
        	}
        	$date = Date("Y-m-d H:i:s");
        	if (isAllowedToPost($uid, $username)) {
                	$dbConn->query("insert into posts (title, text, isTopic, username, date, uid) values ('$postTitle', '$post', 1, '$username', '$date', $uid)");
		print <<<EOF
			<script type="text/javascript">
				window.location.href = '/feed'; 
			</script>  
EOF;
		} else {
			include("tooManyPosts.php");
		}
	}
	
	print <<<EOF
	 <form action=newPost method='POST'>
	 <table style="margin-bottom: 5vh !important" class="mainContent">
		<tr>
                <td>
                <div class="form-floating">
                <input placeholder="Title" style="margin-bottom: 10px; height: 10vh" name="postTitle" class="form-control" id="floatingTextInput"></input>
		<label for="floatingTextInput">Title</label>
		</div>
		<div class="form-floating">
		<textarea placeholder="Enter your post here" style="margin-bottom: 50px; height: 30vh" name="post" class="form-control" id="floatingTextArea"></textarea>
                <label for="floatingTextArea">Post</label>
		</div>
                </td>
		</tr>
		<tr>
		<td>
		<textarea style="display: none" name=token ></textarea>
        <button class="btn btn-primary" type="submit">Post</button>
        </td>
        </tr>
        </table>
        </form>

EOF;
	// Token hide
	print <<<EOF
                <script>
                        let x = document.getElementsByName("token")[0];
                        var value = localStorage.getItem("forumToken");
                        x.innerText = value;
                </script>
EOF;



?>	
