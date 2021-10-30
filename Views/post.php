<?php
	$parts = parse_url($URL);
	$parts = explode("/", $parts['path']);
	$postId = mysql_real_escape_string($parts[2]);
	
	global $dbConn;
	
	$post = $dbConn->query("SELECT * FROM posts WHERE pid = $postId LIMIT 1");
	$row = mysql_fetch_assoc($post);
	$title = $row['title'];
	
	$text = $row['post'];
	print <<<EOF
	<body style="background-color: #DDDDDD; margin-left: 20vw; margin-top: 10vh;"> 
	<div style="background-color: white; 
	padding: 20px; padding-bottom: 50px; border-radius: 10px; width: 60vw" class="mainContent post">
	<h3 style="margin-left: 10px">
	$title
	</h3>
	<p style="margin-left: 10px">
	$text
	</p>
	</div>
	</body>
EOF;



        if($_REQUEST['text']) {
		$text = mysql_real_escape_string($_REQUEST['text']);
		$date = Date("Y-m-d H:i:s");
		$parts = parse_url($URL);
		$parts = explode("/", $parts['path']);
		$postId2 = mysql_real_escape_string($parts[2]);
		
		$dbConn->query("INSERT INTO replies(text, opid, date) values('$text', $postId, '$date')");
	}

	
	// TODO: prevent > 1000 replies
	$repliesResult = $dbConn->query("SELECT * FROM replies WHERE opid = $postId
	ORDER BY date asc LIMIT 1000"); 

	print "<div class=\"mainContent\">";
while ($row = mysql_fetch_assoc($repliesResult)) {
        $userName = $row['userId']; // need to query for the username, or could store username in this table
        $text = htmlspecialchars($row['text']);
        $date = htmlspecialchars($row['date']);
	// TODO: likes/dislikes
	print <<<EOF
	<div style="background-color: white; border-radius: 10px; padding: 20px; 
	width: 60vw; margin-bottom: 20px"> 
	<h3>
		$userName
	</h3>	
	<p> $text </p>
	<p> $date </p>
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
          <button class="btn btn-primary" type="submit">Reply</button>
          </td>
          </tr>
          </table>
          </form>
EOF;



?>
