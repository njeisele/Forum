<?php
	require("authentication.php");

	
	 function isAllowedToPost($uid, $username) {
                global $dbConn;
                /* prevent same user from posting too often */
                $maxPostsPerMinute = 10;
                $dateOneMinuteAgo = mysql_real_escape_string(date('Y-m-d H:i:s', strtotime('-1 minute')));
                $result = $dbConn->query("SELECT COUNT(*) from posts WHERE (username = '$username' OR uid = $uid) and date > '$dateOneMinuteAgo'");
		$count = mysql_fetch_row($result)[0];
                return ($count <= $maxPostsPerMinute);
        }

	function getVoteCount($vote, $pid) {
		global $dbConn;
		$result = $dbConn->getSingle("SELECT COUNT(*) from votes WHERE
					      pid = $pid AND status = '$vote'");
		return $result;
	}	

	// TODO: Currently this is recieving the user who is being liked, needs to be fixed
	if (isset($_REQUEST['vote']) && isset($_REQUEST['pid']) && isset($_REQUEST['username'])) {
        	global $dbConn;
        	$vote = mysql_real_escape_string($_REQUEST['vote']);
        	$pid = mysql_real_escape_string($_REQUEST['pid']);
		$username = mysql_real_escape_string($_REQUEST['username']);
       		$dbConn->query("insert into votes(username, pid, status) values('$username', $pid, '$vote')");
        	// Update in case already exists
        	$dbConn->query("update votes SET status='$vote' WHERE username = '$username' and pid = $pid");            
	}



	$parts = parse_url($URL);
	$parts = explode("/", $parts['path']);
	$postId = mysql_real_escape_string($parts[2]);
	
	global $dbConn;
	
	$post = $dbConn->query("SELECT * FROM posts WHERE pid = $postId LIMIT 1");
	$row = mysql_fetch_assoc($post);
	$title = $row['title'];
	$opidUser = $row['username'];
	$opidDate = $row['date'];
		
	$text = $row['text'];
	print <<<EOF
	<body style="background-color: #DDDDDD">
	
EOF;

	 print <<<EOF
		<script>
        document.addEventListener("DOMContentLoaded", function(event) { 
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos, {duration: 0} );
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
        };
    </script>

EOF;


	include("header.php");
	print <<<EOF
	<div style="margin-left: 20vw; margin-top: 10vh;"> 

EOF;

	$likeString = 'like';
        $dislikeString = 'dislike';
        $opidLikeCount = getVoteCount($likeString, $postId);
        $opidDislikeCount = getVoteCount($dislikeString, $postId);


print <<<EOF
	<div style="background-color: white; 
	padding: 20px; padding-bottom: 20px; 
	margin-bottom: 20px;
	border-radius: 10px; width: 60vw" class="mainContent post">
	<h6>$opidUser</h6>
	<h3 style="">
	$title
	</h3>
	<p style="float: right"> $opidDate </p>
	<br />
	<p style=" overflow-wrap: anywhere">
	$text
	</p>
	<a href="/post/$postId?vote=$likeString&pid=$postId&username=$opidUser"   class="fa fa-thumbs-up" style="cursor: pointer; text-decoration: none; font-size:20px;color:green"></a>
        <p style="margin-right: 10px; display: inline-block;">$opidLikeCount</p>
        <a href="/post/$postId?vote=$dislikeString&pid=$postId&username=$opidUser"  class="fa fa-thumbs-down" style="cursor: pointer; text-decoration: none; font-size:20px;color:red"></a>
        $opidDislikeCount
	</div>
	</body>
EOF;
	
	
	if (isset($_REQUEST['token']) && isset($_REQUEST['text'])) {
		$auth = new Auth();
		$decodedToken = $auth->verifyToken($_REQUEST['token']);
		$username = mysql_real_escape_string($decodedToken->data->username);
		$text = $_REQUEST['text'];
		$date = Date("Y-m-d H:i:s");
                $ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
                $uid = $dbConn->getSingle("select uid from users where ip = '".$ip."'");
                if (!$uid) {
                        // New IP
                        $dbConn->query("insert into users (ip) values ('$ip')");
                }
                if (isAllowedToPost($uid, $username)) {
		// Below query, null title, 0 for isTopic
   	             $dbConn->query("INSERT INTO posts(text, title, isTopic, opid, date, username, uid) values('$text', NULL, 0, $postId, '$date', '$username', $uid)");
		} else {
			include("tooManyPosts.php");
		}
	}	

	
	// prevent > 1000 replies
	$repliesResult = $dbConn->query("SELECT * FROM posts WHERE opid = $postId
	ORDER BY date asc LIMIT 1000"); 

	print "<div class=\"mainContent\">";
while ($row = mysql_fetch_assoc($repliesResult)) {
        $text = htmlspecialchars($row['text']);
        $date = htmlspecialchars($row['date']);
	$username = htmlspecialchars($row['username']);
	$pid = htmlspecialchars($row['pid']);	
	
	$likeString = 'like';
        $dislikeString = 'dislike';
	
	$likeCount = getVoteCount($likeString, $pid);
	$dislikeCount = getVoteCount($dislikeString, $pid);

	print <<<EOF
	<div style="background-color: white; border-radius: 10px; padding: 20px; 
	width: 60vw; margin-bottom: 20px"> 
			<div style="width: 100%; display: inline"> <h6 style="display: inline-block; float: left">$username</h6> <p style="float: right; display: inline-block">$date</p></div>	
			<p style="overflow-wrap: anywhere; padding-top: 40px"> $text </p>

	<a href="/post/$postId?vote=$likeString&pid=$pid&username=$username"   class="fa fa-thumbs-up" style="cursor: pointer; text-decoration: none; font-size:20px;color:green"></a>
	<p style="margin-right: 10px; display: inline-block;">$likeCount</p>
	<a href="/post/$postId?vote=$dislikeString&pid=$pid&username=$username"  class="fa fa-thumbs-down" style="cursor: pointer; text-decoration: none; font-size:20px;color:red"></a>
	$dislikeCount
	</div>
EOF;
}

print "</div>";

print <<<EOF
	   
           <form action=$postId method='POST'>
           <table style="margin-bottom: 5vh !important" class="mainContent">
                 <tr>
                  <td>
                  <div class="form-floating">
                  <textarea style="width: 60vw; margin-bottom: 10px; height: 20vh" name=text class="form-control" id="floatingTextArea"></textarea>
                  </div>
                  </td>
                 </tr>
                  <tr>
                  <td>
		  <textarea style="display: none" name=token ></textarea>
          <button class="btn btn-primary" type="submit">Reply</button>
          </td>
          </tr>
          </table>
          </form>
EOF;

	// Sneaky token hide
	print <<<EOF
                <script>
               		let x = document.getElementsByName("token")[0];
			var value = localStorage.getItem("forumToken");
			x.innerText = value;
                </script>
EOF;



?>
