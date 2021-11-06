<?php

	print <<<EOF
	<div style="width: 100%; text-align: right; padding-right: 5vw; background-color: #BBBBBB;
	padding-bottom: 20px; padding-top: 20px;" ><h6 id=myUsername></h6></div>
EOF;

	 print <<<EOF
		<script>
			const username = localStorage.getItem("forumUsername");
			document.getElementById("myUsername").innerText = username;
		</script>
EOF;

?>
